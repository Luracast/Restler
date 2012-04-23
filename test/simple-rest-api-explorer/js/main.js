jQuery(function($) {
	
	var makeCall = function(apiUrl, requestMethod, extraData) {
		
		// show spinnner
		$('#loading').show();
		
		// make the call
		$.ajax({
			url : apiUrl + apiUrlSuffix,
			type: requestMethod,
			dataType : apiDataType,
			headers: apiExtraHeaders,
			data : extraData
		}).success(function(data, textStatus, jqXHR) {
			if (data) {
				$.JSONView(data, $('#result'));
			} else {
				$.JSONView({}, $('#result'));
				$('#result').html('<ol><li>Server returned no data. (HTTP Status Code: ' + jqXHR.status + ' – ' + jqXHR.statusText + ')</li></ol>'); // 204 for example
			}
			$('#loading').hide();
		}).error(function(jqXHR, textStatus, errorThrown) {
			$.JSONView(jqXHR.responseText ? JSON.parse(jqXHR.responseText) : {}, $('#result'));
			$('#loading').hide();
		});
	}
	
	// hook sidebar clicks
	$('#sidebar').on('click', 'a', function(e, dontpush) {
		e.preventDefault();
		if ($('#loading').is(':hidden')) {
			$('#sidebar a').removeClass('active');
			$(this).addClass('active');
			var url = $(this).attr('href');
			var requestmethod = $(this).data('requestmethod').toLowerCase() || 'get';
			$('#apiurl').val(apiBaseUrl + url);
			$('#requestmethod').val(requestmethod.toUpperCase());
			!dontpush && history.pushState({'href': url, 'method': requestmethod}, 'clicked ' + url, '#' + requestmethod + '|' + url);
			makeCall(apiBaseUrl + url, requestmethod, $(this).data('extradata') || {});
		}
	});
		
	// hook form submit
	$('form').on('submit', function(e, dontpush){
		e.preventDefault();
		if ($('#apiurl').val().indexOf(apiBaseUrl) == 0) {
			var url = $('#apiurl').val().replace(apiBaseUrl,'');
			var method = $('#requestmethod').val().toLowerCase();
			$('#sidebar a').removeClass('active');
			$('#sidebar a[href="' + url + '"][data-requestmethod="' + method + '"]').addClass('active');
			!dontpush && history.pushState({'href': url, 'method': method}, 'clicked ' + url, '#' + method + '|' + url);
			makeCall(apiBaseUrl + url, $('#requestmethod').val());
		} else {
			alert('You are only allowed make calls to ' + apiBaseUrl);
		}
	});
	
	// History Popstate
	$(window).on('popstate', function(e) {
		e.preventDefault();
		$('#sidebar a').removeClass('active');
		
		// actual popstate
		if (e.originalEvent.state && e.originalEvent.state.href) {
			
			// fill form & submit (but don't push on history stack)
			$('#apiurl').val(apiBaseUrl + e.originalEvent.state.href);
			$('#requestmethod').val(e.originalEvent.state.method.toUpperCase());
			$('form').trigger('submit', true);
			
		}
		
		// page load
		else {
			
			// hash set
			if (window.location.hash && (window.location.hash.indexOf('|') > -1)) {
				
				// extract method & url
				var method = window.location.hash.substr(1).split('|')[0].toLowerCase();
				var url = window.location.hash.substr(1).split('|')[1];
				
				// fill form & submit (but don't push on history stack)
				$('#apiurl').val(apiBaseUrl + url);
				$('#requestmethod').val(method.toUpperCase());
				$('form').trigger('submit', true);

			} 
			
			// no has set: call first link
			else {
				$('#sidebar a:first').addClass('active').trigger('click', true);
			}
		}
	});
	
});