/**
*
* data table api : https://datatables.net/
*
*/


$(document).ready( function () {
    MainContents.init();
} );

var MainContents = (function (){
    var _jsonData_1 = {}			// 요약 테이블 데이터
    var _jsonData_2 = {}			// 자세히 보기 테이블 테이터
	var _arrJsonTeamData = [];		// 각 팀 데이터
	var _arrTables = [];			// 테이블 API로 생성된 객체가 담길 배열
    var _totalLen = 0;				// 총 데이터 갯수			
	var _tableLen = 0;				// 테이블 갯수
	var _selectedIndex = 0;			// 현재 선택된 탭 인덱스
    var STATIC_CELL_NUM = 3;		// 고정된 셀 갯수
	var STATIC_TAB_MENU_NUM = 2;	// 고정된 탭 메뉴 갯수

    var _init = function(){        
		var paramObj = _getUrlParameter();
		var rn = (paramObj.rn == undefined || paramObj.rn == '')?2:paramObj.rn;
        var url = "./json/df_chart_total_"+week+".json?rn="+rn;
        // var url = "./json/total.json";
		var data ={};

        _getJsonData(url, data, _getDataComplete);
    }

	
	// JSON 데이터 가져오기 완료
    var _getDataComplete = function(json){
		// 요약 테이블
        _jsonData_1 = json.project_member[0];
        _jsonData_1.table_header.lists.unshift({"label":"MM","members":[]});
        _jsonData_1.table_header.lists.unshift({"label":"PM","members":[]});
        _jsonData_1.table_header.lists.unshift({"label":"Project","members":[]});

		
		// 자세히보기 테이블
		_jsonData_2 = json.project_member[1];
        _jsonData_2.table_header.lists.unshift({"label":"MM","members":[]});
        _jsonData_2.table_header.lists.unshift({"label":"PM","members":[]});
        _jsonData_2.table_header.lists.unshift({"label":"Project","members":[]});

		// 요약 데이터 가공하기
        _makeJsonSummaryData();

		// 팀 JSON 데이터 만들기
		_makeJsonTeamData();

		// 탭 메뉴 만들기
		_addTabMenu();

		// 테이블 갯수
		_tableLen = _arrJsonTeamData.length+STATIC_TAB_MENU_NUM;

		// 테이블 데이터 넣어주기
		for(var i=0; i<_tableLen; i++){
			var index = i+1;
			var thead = '#project_member_'+index+' thead';
			var tbody = '#project_member_'+index+' tbody';
			var jsonData = {};
			
			if(i==0){
				jsonData = _jsonData_1;
			}else if(i==1){
				jsonData = _jsonData_2;
			}else{
				jsonData = _arrJsonTeamData[i-STATIC_TAB_MENU_NUM].project_member;
			}

			_addTableHeader(thead, jsonData, i);
			_addTableData(tbody, jsonData, i);
		}


		// 테이블 생성
        window.setTimeout(function(){
			for(var i=1; i<=_tableLen; i++){
				// MainContents.addTable(i);
			}
			_startTable();
		},200);
    }

    var _makeJsonSummaryData = function() {
        var i;
        console.log(_jsonData_1);
        var toSum = 0;
        var tmpToData;
        for (i=0;i<_jsonData_1.table_data.lists.length;i++) {
            tmpToData = _jsonData_1.table_data.lists[i];
            tmpToData.to = parseFloat(tmpToData.to.split('(')[0]);
            // console.log(tmpToData.to);

            toSum += tmpToData.to;
        }
        console.log(toSum);

        for (i=0;i<_jsonData_1.table_data.lists.length;i++) {
            tmpToData = _jsonData_1.table_data.lists[i];
            tmpToData.to = Math.round((tmpToData.to/toSum)*100) + '%';
        }


    };

	// 팀별 JSON 데이터 만들기
	var _makeJsonTeamData = function(){
		var teamLen = _jsonData_2.table_header.lists.length - STATIC_CELL_NUM;
		var startNum = 0;
		for(var i=0; i<teamLen; i++){
			var teamJsonData = {
				"project_member": {
					"table_header": {
						"lists": [
							{
								"label":"Project",
								"members":[]
							}
							,
							{
								"label":"PM",
								"members":[]
							},
							{
								"label":"MM",
								"members":[]
							}
						]
					},
					"table_data": {
						"lists": []
					}
				}
			};

			// 각 팀별 테이블 헤더 데이터 생성
			var th = _jsonData_2.table_header.lists[i+STATIC_CELL_NUM]
			teamJsonData.project_member.table_header.lists.push(th);
		
			
			// 각 팀별 테이블 데이터 복사
			// 데이터 복사에 관한 글
			// https://hyunseob.github.io/2016/02/08/copy-object-in-javascript/
			// https://poiemaweb.com/js-immutability

			var memberLen = _jsonData_2.table_header.lists[i+STATIC_CELL_NUM].members.length;
			teamJsonData.project_member.table_data.lists = $.extend(true, [], _jsonData_2.table_data.lists);
			

			
			// 테이블 데이터에서 포인트 데이터만 팀에 맞게 넣어주기
			var tdLen = _jsonData_2.table_data.lists.length;
			for(var j=0; j<tdLen; j++){
				teamJsonData.project_member.table_data.lists[j].points = [];				
				for(var k=startNum; k<(memberLen+startNum); k++){
					var point = _jsonData_2.table_data.lists[j].points[k];
					teamJsonData.project_member.table_data.lists[j].points.push(point);										
				}
			}			
			startNum = startNum + memberLen;
			_arrJsonTeamData.push(teamJsonData);
		}


	};

	// tab메뉴 만들기
	var _addTabMenu = function(){
		var len = _jsonData_1.table_header.lists.length - STATIC_CELL_NUM + STATIC_TAB_MENU_NUM;
		var ele = '';
		var eleTable = '';
		for(var i=0; i<len; i++){
			if(i==0){
				// 요약 메뉴
				ele = ele + '<li class="is-active btn-tab"><a>요약</a></li>'
			}else if(i==1){
				// 자세히보기 메뉴
				ele = ele + '<li class="btn-tab"><a>자세히</a></li>';
			}else{
				// 팀명 메뉴
				ele = ele + '<li class="btn-tab"><a>'+_jsonData_1.table_header.lists[i+1].label+'</a></li>';
			}

			var index = i+1;
			var id = 'project_member_'+index;
			eleTable = eleTable +'<div class="container-tbl tbl-'+index+'">'+
									'<table id="'+id+'" class="table is-bordered is-hoverable" style="width:100%">'+
										'<thead></thead>'+
										'<tbody></tbody>'+
									'</table>'+
								  '</div>'
			
			
		}
		$('.tabs ul').html(ele);
		$('.table-area').html(eleTable);


	}
	

    // 헤더 만들기
    var _addTableHeader = function(target, jsonData, tableIndex){
        var ROW_SPAN_MAX = (tableIndex == 0)?2:3;

        for(var i=0; i<ROW_SPAN_MAX; i++){
            $(target).append('<tr></tr>');
            var ele = "";
            var titleLen = jsonData.table_header.lists.length;

            if(i==0){
				// 테이블 헤더 가장 상위
                var columnNum = 3;
                var totalNum = 0;
                for(var j=0; j<titleLen; j++){

                    //팀 표일 경우 PM MM 생략
                    if (tableIndex>1) {
                        if (j===1) continue;
                        if (j===2) continue;
                    }


                    if(j<STATIC_CELL_NUM){
                        var className = (j==0)?'':'';

                        if (j==0) {
                            ele = ele + '<th colspan="2" rowspan="' + ROW_SPAN_MAX + '" class="' + className + '">' + jsonData.table_header.lists[j].label + '</th>';
                        } else {
                            ele = ele + '<th rowspan="' + ROW_SPAN_MAX + '" class="' + className + '">' + jsonData.table_header.lists[j].label + '</th>';
                        }
                    }else{
                        var memberLen = jsonData.table_header.lists[j].members.length;
						var memberName = jsonData.table_header.lists[j].members[0].name;

                        columnNum = columnNum + 1;
                        totalNum = totalNum + memberLen;
						if(memberName != ""){
							ele = ele + '<th colspan="'+memberLen+'">'+jsonData.table_header.lists[j].label+'</th>';
						}else{
							ele = ele + '<th>'+jsonData.table_header.lists[j].label+'</th>';
						}
                    }
					
                    if(j==titleLen-1) _totalLen = totalNum;
                }
            }else{
				// 2번째 또는 3번째 행
                for(var j=0; j<titleLen; j++){
                    var memberLen = jsonData.table_header.lists[j].members.length;
                    if(memberLen > 0){
                        for(var k=0; k<memberLen; k++){
                            if(i==1 && tableIndex != 0){
                                // 멤버
                                var memberName = jsonData.table_header.lists[j].members[k].name;
                                if(memberName != ""){
                                    ele = ele + '<th>'+memberName+'</th>'
                                }
                            }else{
                                // 가용시간
                                var memberPoint = jsonData.table_header.lists[j].members[k].point;
                                ele = ele + '<th>'+memberPoint+'</th>'
                            }
                        }
                    }
                }
            }
			
            $(target+' tr').eq(i).html(ele);
        }
    };

    // 테이블 데이터 만들기
    var _addTableData = function(target, jsonData, tableIndex){
        var listLen = jsonData.table_data.lists.length;
        var dataLen = _totalLen+3;

        for(var i=0; i<listLen; i++){
            var trEl = $('<tr></tr>');
            var ele = "";
            var sum = 0;
            var j;

            for (j = 0; j < dataLen; j++) {
                if (j < STATIC_CELL_NUM) {

                    //팀 표일 경우 PM MM 생략
                    if (tableIndex>1) {
                        if (j===1) continue;
                        if (j===2) continue;
                    }


                    if (j == 0) {
                        var startTime = jsonData.table_data.lists[i].start_time;
                        var endTime = jsonData.table_data.lists[i].end_time;
                        ;

                        // if (i===0) {
                        ele = ele + ' <td  style="width:300px;">' + jsonData.table_data.lists[i].project_name;
                        // ele = ele + '</td><td style="width:200px;"><span class="is-size-7">' + startTime +' - ' +endTime + '</span></td>';
                        if (endTime === '2019-12-31') {
                            ele = ele + '</td><td style="width:100px;"><span class="is-size-7"> - </span></td>';
                        } else {
                            ele = ele + '</td><td style="width:150px;"><span class="is-size-7"> ~ ' + endTime + '</span></td>';
                        }

                        // } else {
                        //     ele = ele + ' <td style="width:300px;" title="시작일 : '+startTime+'&#10;종료일 : '+endTime+'">'+jsonData.table_data.lists[i].project_name+'</td>'
                        // }
                    } else {
                        var txt = (j == 1) ? jsonData.table_data.lists[i].pm : jsonData.table_data.lists[i].to;
                        ele = ele + ' <td style="width:70px;">' + txt + '</td>'
                    }
                } else {
                    //console.log(j-STATIC_CELL_NUM)
                    var points = (jsonData.table_data.lists[i].points[j - STATIC_CELL_NUM] != "0") ? jsonData.table_data.lists[i].points[j - STATIC_CELL_NUM] : '&nbsp;';

                    points = points.split('(')[0];
                    ele = ele + ' <td >' + points + '</td>';
                    points = points.replace('&nbsp;', '');


                    if (points === '') {
                        points = 0;
                    }
                    // console.log(points);
                    sum += parseFloat(points);
                }
            }


            // console.log(sum);
            if (sum !== 0) {
                trEl.html(ele);
                // $(target + ' tr').eq(i).html(ele);
                $(target).append(trEl);
            }

        }
    }
	
	// 테이블 만들기
    var _addTable = function(index){
		var navBarH = $('.navbar').height();
		var tabH = $('.tabs').height();
		var winH = $(window).height();
		var targetH = (winH-200) - (navBarH+tabH);
		
		var id = '#project_member_'+index
		var table = $(id).DataTable({
			paging:   false,
			ordering: false,
			searching:false,
			info:     false,
			responsive: true,
			scrollX: true,
	

			columnDefs: [
				{ "width": "15%", "targets": 0 },
				{ "width": "5%", "targets": 1 },
				{ "width": "5%", "targets": 2 }
			]

			
		});

		_arrTables.push(table);
    };
	
	// 테이블 시작하기
	var _startTable = function(){
		for(var i=0; i<_tableLen; i++){
			if(i==0){
				$('.container-tbl').eq(i).css('visibility', 'visible');
			}else{
				$('.container-tbl').eq(i).css('visibility', 'visible').css('display', 'none');
			}
		}
        $('.Spinner').css('display', 'none');
		_addEvent();
	};



	// 이벤트 ADD
	var _addEvent = function(){
		$(window).on('resize', _onResize)
		$('.btn-tab').on('click', _onClick_tab);
	}

	var _onResize = function(e){

	}

	// 탭버튼 클릭했을 때
	var _onClick_tab = function(e){
		var index = $(e.currentTarget).index();
		_selectedIndex = index;
		_changeTab(index);
	}

	// 탭 변경
	var _changeTab = function(index){


		var len = $('.container-tbl').length;

		console.log(index + ' ' + len);

		for(var i=0; i<len; i++){
			if(i==index){
				$('.container-tbl').eq(index).css('display', 'block');
				$('.btn-tab').eq(index).addClass('is-active');
			}else{
				$('.container-tbl').eq(i).css('display', 'none');
				$('.btn-tab').eq(i).removeClass('is-active');
			}

			
		}
	}


	// 주소창 파라미터 가져오기
	var _getUrlParameter = function (){
		var ParameterObject = new Object();
		var locate = location.href;

		if(locate.indexOf("?")==-1){
			return ParameterObject;
		}

		var parameter = locate.split("?")[1];
		parameter = parameter.split("#")[0];
		var paramAreay = parameter.split("&");
		for ( var i=0; i<paramAreay.length; i++ )
		{
			var tem = paramAreay[i].split("=");
			ParameterObject[tem[0]] = tem[1];
		}
		getUrlParameter = function () { return ParameterObject; }
		return ParameterObject;
	}
 


    /////////////////////////////////////////////
    //	서버 통신
    /////////////////////////////////////////////
    var _getJsonData = function(url, data, callback){
        var  dType = "json";
        $.ajax({
            url : url,
            data : data,
            dataType: dType,
            error : function(e){
                console.error('json parse error');
            },
            success : function(json){
                callback(json);
            }
        });
    }

    return {
        init : _init,
        getDataComplete : _getDataComplete,
        addTable : _addTable
    }
})();