/**
 *
 * data table api : https://datatables.net/
 *
 */


$(document).ready( function () {
	MainContents.init();
} );

var MainContents = (function (){
	var _jsonData = {};			// 요약 테이블 데이터
	var _totalLen = 0;				// 총 데이터 갯수
	var STATIC_CELL_NUM = 3;		// 고정된 셀 갯수
	var STATIC_TAB_MENU_NUM = 2;	// 고정된 탭 메뉴 갯수

	var _init = function(){
		var paramObj = _getUrlParameter();
		// var rn = (paramObj.rn == undefined || paramObj.rn == '')?2:paramObj.rn;
		// var url = "./json/df_chart_total_"+week+".json?rn="+rn;
		var url = "./json/total.json";
		var data ={};

		_getJsonData(url, data, _getDataComplete);
	};


	// JSON 데이터 가져오기 완료
	var _getDataComplete = function(json){
		// 요약 테이블
		_jsonData = json.project_schedule[0];
		console.log(_jsonData);

		// 요약 데이터 가공하기
		// _makeJsonSummaryData();
		var thead = '#project_calender thead';
		var tbody = '#project_calender tbody';

		_addTableHeader(thead, _jsonData);
		_addTableData(tbody, _jsonData);

		// 테이블 생성
		window.setTimeout(function(){
			_startTable();
		},200);
	};

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

	// 헤더 만들기
	var _addTableHeader = function(target, jsonData){
		var ROW_SPAN_MAX = 2;

		var i, j, k;
		for( i=0; i<ROW_SPAN_MAX; i++){
			$(target).append('<tr></tr>');
			var ele = "";
			var titleLen = jsonData.table_header.lists.length;

			if(i===0)
			{
				ele = ele +  '<th style="vertical-align: middle" rowspan="' + ROW_SPAN_MAX + '" > 부서</th>';
				ele = ele +  '<th style="vertical-align: middle" rowspan="' + ROW_SPAN_MAX + '" > 이름</th>';

				// 테이블 헤더 가장 상위
				for(j=0; j<titleLen; j++){
					ele = ele + '<th colspan="' + jsonData.table_header.lists[j].week.length + '">' + jsonData.table_header.lists[j].month + '</th>';
				}
			} else {
				for (j=0; j<titleLen; j++) {
					for (k=0;k< jsonData.table_header.lists[j].week.length; k++) {
						ele = ele + '<th>' + jsonData.table_header.lists[j].week[k] + '</th>';
					}
				}

			}

			$(target+' tr').eq(i).html(ele);
		}
	};

	// 테이블 데이터 만들기
	var _addTableData = function(target, jsonData){
		var listLen = jsonData.table_data.lists.length;
		var i,j,k;

		for(i=0; i<listLen; i++){
			var trEl = $('<tr></tr>');
			var ele = "";


			for (j = 0; j < jsonData.table_data.lists[i].points.length + 2; j++) {
				if (j === 0) {
					ele = ele + ' <td  style="width:150px;">' + jsonData.table_data.lists[i].name + '</td>';
				} else if (j===1){
					ele = ele + ' <td  style="width:150px;">' + jsonData.table_data.lists[i].part + '</td>';
				} else {
					ele = ele + ' <td style="width:70px;">' + jsonData.table_data.lists[i].points[j-2] + '</td>'
				}
			}

			trEl.html(ele);
			$(target).append(trEl);
		}
	};

	// 테이블 시작하기
	var _startTable = function(){
		$('.Spinner').css('display', 'none');
		_addEvent();
	};

	// 이벤트 ADD
	var _addEvent = function(){
		$(window).on('resize', _onResize)
	};

	var _onResize = function(e){

	};

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
	};



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
	};

	return {
		init : _init,
		getDataComplete : _getDataComplete
	}
})();