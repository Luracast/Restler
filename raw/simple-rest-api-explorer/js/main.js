jQuery(function($) {
    $('#result-edit').hide();
	var makeCall = function(apiUrl, requestMethod, extraData) {

        // show spinnner
		$('#loading').show();
        if ($.isArray(extraData)) {
            data = {};
            for (var key in extraData) {
                d = extraData[key].split('=');
                data[d[0]] = d[1];
            }
            extraData = JSON.stringify(data, null, 4);
        }

		// make the call
        request(requestMethod, apiUrl)
            .set('Accept', 'application/json')
            .send(extraData)
            .type(apiDataType)
            .end(function(res){
                $('#res-status').html(res.status).attr('class', 'status-type'+res.statusType);
                head = '';
                for (var key in res.header) {
                    head += '<dt>'+key+':</dt><dd>' + res.header[key] + ';</dd>';
                }
                $('#res-result').html(head);

                if (res.statusType > 2) {
                    $.JSONView(res.text ? res.body : {}, $('#result'));
                    $.JSONEdit(res.text ? res.body : {}, $('#result-edit'));
                    $('#loading').hide();
                }
                else if (!res.body) {
                    $.JSONView({}, $('#result'));
                    $.JSONEdit({}, $('#result-edit'));
                    $('#result').html('<ol><li>Server returned no data. (HTTP Status Code: ' + res.status + ' – ' + res.text + ')</li></ol>'); // 204 for example
                } else {
                    if (requestMethod == 'post') {
                        $('#sidebar-put').attr('href', '/authors/'+res.body.id);
                        $('#sidebar-del').attr('href', '/authors/'+res.body.id);
                        $('#apiurl').val($('#apiurl').val() + '/' + res.body.id);
                        console.log($('#sidebar-del').attr('href'));
                    }
                    $.JSONView(res.body, $('#result'));
                    $.JSONEdit(res.body, $('#result-edit'));
                }
                $('#loading').hide();
            });
    };

	// hook sidebar clicks
	$('#sidebar').on('click', 'a', function(e, dontpush) {
		e.preventDefault();
		if ($('#loading').is(':hidden')) {
			$('#sidebar a').removeClass('active');
			$(this).addClass('active');
			var url = $(this).attr('href');
			var requestmethod = $(this).data('requestmethod') ? $(this).data('requestmethod').toLowerCase() : 'get';
			$('#apiurl').val(apiBaseUrl + url);
			$('#requestmethod').val(requestmethod.toUpperCase());
			!dontpush && history.pushState({'href': url, 'method': requestmethod}, 'clicked ' + url, '#' + requestmethod + '|' + url);
            data = $(this).data('extradata') ? $(this).data('extradata').split('&') : {};
            makeCall(apiBaseUrl + url, requestmethod, data );
        }
	});

	// hook form submit
	$('form').on('submit', function(e, dontpush){
		e.preventDefault();
        var url = $('#apiurl').val().replace(apiBaseUrl,'');
        var method = $('#requestmethod').val().toLowerCase();
        makeCall(apiBaseUrl + url, method, $('#result-edit').val());
	});

    $('#result').click(function (e) {
       $(this).hide();
       $('#result-edit').show();
    });

    $('#result-edit').focusout(function (e) {
        $(this).hide();
        try {
            ob = JSON.parse($(this).val());
            $(this).val(JSON.stringify(ob,null,4));
            $.JSONView(ob, $('#result'));
            $('#result').show();
            $(this).hide();
        } catch (e) {
            $(this).show().focus();
            alert('There was an error trying to parse the supplied data.\n\n' + e.toString()
            + '\n\nPlease verify and correct the problem before proceeding.');
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
				$('#sidebar-list').addClass('active').trigger('click', true);
			}
		}
	});

});
