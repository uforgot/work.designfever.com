/*
	* @author : Jin-Yong, Joo
	* @date : July 07, 2014
	* @comment : Calendar.js
	* @version : 0.10	
*/

var Calendar = Calendar || ( function(){
	var _monthDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
	var _date = new Date();
	var _day = _date.getDate();
	var _month = _date.getMonth()+1;
	var _year = _date.getFullYear();
	var _html = '';
	var _container = '';
	var _callBack = '';

	// private -------------------------------------------

	function _clickPrev1( $evt ) {
		$evt.preventDefault();

		_year--; 
		update( _year, _month, _day );
	}

	function _clickPrev2( $evt ) {
		$evt.preventDefault();

		_month--; 
		if( _month < 1 ) { 
			_year--; 
			_month = 12; 
		} 

		update( _year, _month, _day );
	}

	function _clickNext1( $evt ) {
		$evt.preventDefault();

		_month++; 
		if( _month > 12 ) { 
			_year++; 
			_month = 1; 
		} 

		update( _year, _month, _day );
	}

	function _clickNext2( $evt ) {
		$evt.preventDefault();

		_year++; 
		update( _year, _month, _day );
	}

	function _clickDay( $evt ) {
		$evt.preventDefault();

		_callBack( _year, _month, $(this).text() );
	}

	// public -------------------------------------------

	function init( $container, $callBack ) {
		_container = $container;
		_callBack = $callBack;
		update( _year, _month, _day );
		$('.prev1').on( 'click', _clickPrev1 );
		$('.prev2').on( 'click', _clickPrev2 );
		$('.next1').on( 'click', _clickNext1 );
		$('.next2').on( 'click', _clickNext2 );
	}

	function update( $year, $month, $day ) {
		$('.infoToday').text( $year+'.'+$month );

		if( ( $year%4==0 || $year%100 == 0 ) && ($year%400==0) ) {
			_monthDays[1] = 29;

		} else {
			_monthDays[1] = 28;
		}

		_html = "<table>" 
		_html += "<tr bgcolor=white>" 
		var firstDay = new Date( $year, $month-1, 1 ).getDay();
		for( var i=0; i<firstDay; i++ ) {
			_html += "<td> ";
		}

		var dayCount = 1;
		var dayTotal = _monthDays[ $month-1 ];

		for( var i=0; i<dayTotal; i++ ) {
			_html += ( dayCount == $day ) ? "<td bgcolor='#f2f2f2'>" : "<td>";
			_html += ( (i+firstDay)%7 == 0 ) ? "<a href='#' style='color:#eb6100'>" : "<a href='#'>";
			_html += dayCount++;
			_html += "</a>" 

			if( ((i+1)+firstDay)%7 == 0 ) {
				_html += "<tr bgcolor=white>";
			}
		} 

		var dayFirstTotal = dayTotal + firstDay;
		var weekTotal = ( dayFirstTotal > 28 ) ? ( dayFirstTotal > 35 ? 42 : 35 ) : 28;

		if( dayFirstTotal > 7 * 4 ) {
			if( dayFirstTotal > 35 ) {
				weekTotal = 7 * 6;

			} else {
				weekTotal = 7 * 5;
			}

		} else {
			weekTotal = 28;
		}


		var addCells = weekTotal - dayFirstTotal;
		for( var i=0; i<addCells; i++ ) {
			_html += "<td>";
		}

		_html += "</table><br>";
		_container.html( _html );
		_container.find('a').on( 'click', _clickDay );
	} 

	function year() { return _year; }
	function month() { return _month; }
	function day() { return _day; }

	return {
		init: init, 
		reset: update, 
		year: year, 
		month: month, 
		day: day
	}
})();