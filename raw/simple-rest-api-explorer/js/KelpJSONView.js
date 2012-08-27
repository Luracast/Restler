/*
 * Kelp JSONView – http://kelp.phate.org/2011/11/kelp-json-view-json-syntax-highlighting.html
 * Modded by Bramus! - http://www.bram.us/
 */
$.extend(jQuery,
{
    // json ¥i¶Ç¤J json ©Î JavaScript Object
    // container ¬°¿é¥Xªº®e¾¹¡AjQuery Object
    JSONView: function (json, container) {
        var ob = (typeof json == 'string') ? JSON.parse(json) : json,
		    p, 
		    l = [], 
		    c = container;
		
        var repeat = function (s, n) {
            return new Array(n + 1).join(s);
        };

		// Check whether a string is an URL – Added by Bramus!
		var isUrl = function(s) {
			var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
			return regexp.test(s);
		};
		
		// Escape string for output – Added by Bramus!
		var htmlEntities = function(str) {
		    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		}
		
        //²£¥Í JSON µ²ºc¸ê®Æªº»¼°j¨ç¼Æ
        //o     ¨Ó·½ª«¥ó
        //isar  ¸ê®Æ¬O true ªº¸Ü¥Nªí³o¤@¦¸»¼°j¬°°}¦C¸ê®Æ
        //s     »¼°j¶¥¼h¼Æ
        var r = function (o, isar, s) {
            for (var n in o) {
                var p = o[n];
                switch (typeof p) {
                    case 'function':
                        break;
                    case 'string':
						p = isUrl(p) ? '<a href="' + p + '">' + p + '</a>' : htmlEntities(p);
                        if (isar)
                            l.push({ Text: '<span class="jsonstring">"' + p + '"</span><span class="jsontag">,</span>', Step: s });
                        else
                            l.push({ Text: '<span class="jsonname">"' + n + '"</span><span class="jsontag">: </span><span class="jsonstring">"' + p + '"</span><span class="jsontag">,</span>', Step: s });
                        break;
                    case 'boolean':
                        if (isar)
                            l.push({ Text: '<span class="jsonboolean">"' + p + '"</span><span class="jsontag">,</span>', Step: s });
                        else
                            l.push({ Text: '<span class="jsonname">"' + n + '"</span><span class="jsontag">: </span><span class="jsonboolean">' + p + '</span><span class="jsontag">,</span>', Step: s });
                        break;
                    case 'number':
                        if (isar)
                            l.push({ Text: '<span class="jsonnumber">' + p + '</span><span class="jsontag">,</span>', Step: s });
                        else
                            l.push({ Text: '<span class="jsonname">"' + n + '"</span><span class="jsontag">: </span><span class="jsonnumber">' + p + '</span><span class="jsontag">,</span>', Step: s });
                        break;
                    case 'object':
                        if (p === null) {
                            if (isar)
                                l.push({ Text: '<span class="jsonnull">' + p + '</span><span class="jsontag">,</span>', Step: s });
                            else
                                l.push({ Text: '<span class="jsonname">"' + n + '"</span><span class="jsontag">: </span><span class="jsonnull">' + p + '</span><span class="jsontag">,</span>', Step: s });
                        }
                        else if (p.length == undefined) {
                            //object
                            if (!isar) {
                                l.push({ Text: '<span class="jsonname">"' + n + '"</span><span class="jsontag">:</span>', Step: s });
                            }
                            l.push({ Text: '<span class="jsontag">{</span>', Step: s });
                            r(p, false, s + 1);
                            l.push({ Text: '<span class="jsontag">},</span>', Step: s });
                        }
                        else {
                            //array
                            if (!isar) {
                                l.push({ Text: '<span class="jsonname">"' + n + '"</span><span class="jsontag">:</span>', Step: s });
                            }
                            l.push({ Text: '<span class="jsontag">[</span>', Step: s });
                            r(p, true, s + 1);
                            l.push({ Text: '<span class="jsontag">],</span>', Step: s });
                        }
                        break;
                    default: break;
                }
            }
            var last = l.pop();
            var ct = ',</span>';
            if (last.Text.substr(last.Text.length - ct.length) == ct)
                l.push({ Text: last.Text.replace(ct, '</span>'), Step: last.Step });
            else
                l.push(last);
        };

        //±N JavaScript Object ®æ¦¡¤Æ¶ë¶i array ¤¤
        if (ob.length == undefined) {
            //object
            l.push({ Text: '<span class="jsontag">{</span>', Step: 0 });
            r(ob, false, 1);
            l.push({ Text: '<span class="jsontag">}</span>', Step: 0 });
        }
        else {
            //array
            l.push({ Text: '<span class="jsontag">[</span>', Step: 0 });
            r(ob, true, 1);
            l.push({ Text: '<span class="jsontag">]</span>', Step: 0 });
        }

        // Build HTML String
        var html = '<ol>';
        for (var index in l) {
            var jobject = l[index];
            html += '<li>' + repeat(' &nbsp; &nbsp;', jobject.Step) + jobject.Text + '</li>';
        }
		html += '</ol>';
		
		// Inject HTML String into container
		c.addClass('KelpJSONView').html(html);
		
    }
});