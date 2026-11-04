$(function() {

	// Helper function for vertically aligning DOM elements
	// http://www.seodenver.com/simple-vertical-align-plugin-for-jquery/
	$.fn.vAlign = function() {
		return this.each(function(i){
		var ah = $(this).height();
		var ph = $(this).parent().height();
		var mh = (ph - ah) / 2;
		$(this).css('margin-top', mh);
		});
	};

	$.fn.stretchFormtasticInputWidthToParent = function() {
		return this.each(function(i){
		var p_width = $(this).closest("form").innerWidth();
		var p_padding = parseInt($(this).closest("form").css('padding-left') ,10) + parseInt($(this).closest("form").css('padding-right'), 10);
		var this_padding = parseInt($(this).css('padding-left'), 10) + parseInt($(this).css('padding-right'), 10);
		$(this).css('width', p_width - p_padding - this_padding);
		});
	};

	$('form.formtastic li.string input, form.formtastic textarea').stretchFormtasticInputWidthToParent();

	// Vertically center these paragraphs
	// Parent may need a min-height for this to work..
	$('ul.downplayed li div.content p').vAlign();

	// When a sandbox form is submitted..
	$("form.sandbox").submit(function(){

		var error_free = true;

		// Cycle through the forms required inputs
 		$(this).find("input.required").each(function() {

			// Remove any existing error styles from the input
			$(this).removeClass('error');

			// Tack the error style on if the input is empty..
			if ($(this).val() == '') {
				$(this).addClass('error');
				$(this).wiggle();
				error_free = false;
			}

		});

		return error_free;
	});

});

function clippyCopiedCallback(a) {
  $('#api_key_copied').fadeIn().delay(1000).fadeOut();

  // var b = $("#clippy_tooltip_" + a);
  // b.length != 0 && (b.attr("title", "copied!").trigger("tipsy.reload"), setTimeout(function() {
  //   b.attr("title", "copy to clipboard")
  // },
  // 500))
}

// Logging function that accounts for browsers that don't have window.console
function log() {
  if (window.console) console.log.apply(console,arguments);
}
  
var Docs = {

	shebang: function() {

		// If shebang has an operation nickname in it..
		// e.g. /docs/#!/words/get_search
		var fragments = $.param.fragment().split('/');
		fragments.shift(); // get rid of the bang

		switch (fragments.length) {
			case 1:
				// Expand all operations for the resource and scroll to it
//				log('shebang resource:' + fragments[0]);
				var dom_id = 'resource_' + fragments[0];

				Docs.expandEndpointListForResource(fragments[0]);
				$("#"+dom_id).slideto({highlight: false});
				break;
			case 2:
				// Refer to the endpoint DOM element, e.g. #words_get_search
//				log('shebang endpoint: ' + fragments.join('_'));

                // Expand Resource
                Docs.expandEndpointListForResource(fragments[0]);
                $("#"+dom_id).slideto({highlight: false});

                // Expand operation
				var li_dom_id = fragments.join('_');
				var li_content_dom_id = li_dom_id + "_content";

//                log("li_dom_id " + li_dom_id);
//                log("li_content_dom_id " + li_content_dom_id);

				Docs.expandOperation($('#'+li_content_dom_id));
				$('#'+li_dom_id).slideto({highlight: false});
				break;
		}

	},

	toggleEndpointListForResource: function(resource) {
		var elem = $('li[id^=resource_' + Docs.escapeResourceName(resource) + '] ul.endpoints');
		if (elem.is(':visible')) {
			Docs.collapseEndpointListForResource(resource);
		} else {
			Docs.expandEndpointListForResource(resource);
		}
	},

	// Expand resource
	expandEndpointListForResource: function(resource) {
		$('[id^=resource_' + resource + ']').addClass('active');

		var elem = $('li[id^=resource_' + Docs.escapeResourceName(resource) + '] ul.endpoints');
		elem.slideDown();
	},

	// Collapse resource and mark as explicitly closed
	collapseEndpointListForResource: function(resource) {
		$('[id^=resource_' + resource + ']').removeClass('active');

		var elem = $('li[id^=resource_' + Docs.escapeResourceName(resource) + '] ul.endpoints');
		elem.slideUp();
	},

	expandOperationsForResource: function(resource) {
		// Make sure the resource container is open..
		Docs.expandEndpointListForResource(resource);
		$('li[id^=resource_' + Docs.escapeResourceName(resource) + '] li.operation div.content').each(function() {
			Docs.expandOperation($(this));
		});
	},

	collapseOperationsForResource: function(resource) {
		// Make sure the resource container is open..
		Docs.expandEndpointListForResource(resource);
		$('li[id^=resource_' + Docs.escapeResourceName(resource) + '] li.operation div.content').each(function() {
			Docs.collapseOperation($(this));
		});
	},

	escapeResourceName: function(resource) {
		 return resource.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]\^`{|}~]/g, "\\$&")
	},

	expandOperation: function(elem) {
		elem.slideDown();
	},

	collapseOperation: function(elem) {
		elem.slideUp();
	}

};(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['main'] = Handlebars.template({"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<div class='container' id='resources_container'>\n    <ul id='resources'>\n    </ul>\n\n    <div class=\"footer\" style=\"float:right; margin-top: 100px\">\n        <h4 style=\"color: #AAAAAA;\"> "
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"basePath") || (depth0 != null ? lookupProperty(depth0,"basePath") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"basePath","hash":{},"data":data,"loc":{"start":{"line":6,"column":37},"end":{"line":6,"column":49}}}) : helper)))
    + " - <span style=\"font-variant: small-caps\"> powered by <a target=\"_blank\" href=\"http://luracast.com/products/restler/\">Luracast Restler</a> and\n        <a target=\"_blank\" href=\"http://swagger.wordnik.com/\">Swagger UI</a></span>\n        </h4>\n    </div>\n</div>";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['operation'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var stack1, helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n                <h4>Implementation Notes</h4>\n                <p>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"notes") || (depth0 != null ? lookupProperty(depth0,"notes") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"notes","hash":{},"data":data,"loc":{"start":{"line":22,"column":19},"end":{"line":22,"column":30}}}) : helper))) != null ? stack1 : "")
    + "</p>\n                ";
},"1":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n                    <h4>Response Information</h4>\n                    "
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"responseClassSignature") : depth0),{"name":"if","hash":{},"fn":container.program(2, data, 0),"inverse":container.program(3, data, 0),"data":data,"loc":{"start":{"line":26,"column":20},"end":{"line":30,"column":27}}})) != null ? stack1 : "")
    + "\n                ";
},"2":function(container,depth0,helpers,partials,data) {
    var stack1, helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n                        <p><span class=\"model-signature\">"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"responseClassSignature") || (depth0 != null ? lookupProperty(depth0,"responseClassSignature") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"responseClassSignature","hash":{},"data":data,"loc":{"start":{"line":27,"column":57},"end":{"line":27,"column":85}}}) : helper))) != null ? stack1 : "")
    + "</span></p>\n                    ";
},"3":function(container,depth0,helpers,partials,data) {
    var stack1, helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n                        <p>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"responseClass") || (depth0 != null ? lookupProperty(depth0,"responseClass") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"responseClass","hash":{},"data":data,"loc":{"start":{"line":29,"column":27},"end":{"line":29,"column":46}}}) : helper))) != null ? stack1 : "")
    + "</p>\n                    ";
},"4":function(container,depth0,helpers,partials,data) {
    return "\n                    ";
},"5":function(container,depth0,helpers,partials,data) {
    return "\n                    <div class='sandbox_header'>\n                        <input class='submit' name='commit' type='button' value='Try it out!' />\n                        <a href='#' class='response_hider' style='display:none'>Hide Response</a>\n                        <img alt='Throbber' class='response_throbber' src='images/throbber.gif' style='display:none' />\n                    </div>\n                    ";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n    <ul class='operations' >\n        <li class='"
    + alias4(((helper = (helper = lookupProperty(helpers,"httpMethod") || (depth0 != null ? lookupProperty(depth0,"httpMethod") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"httpMethod","hash":{},"data":data,"loc":{"start":{"line":3,"column":19},"end":{"line":3,"column":33}}}) : helper)))
    + " operation' id='"
    + alias4(((helper = (helper = lookupProperty(helpers,"resourceName") || (depth0 != null ? lookupProperty(depth0,"resourceName") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"resourceName","hash":{},"data":data,"loc":{"start":{"line":3,"column":49},"end":{"line":3,"column":65}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":3,"column":66},"end":{"line":3,"column":78}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"httpMethod") || (depth0 != null ? lookupProperty(depth0,"httpMethod") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"httpMethod","hash":{},"data":data,"loc":{"start":{"line":3,"column":79},"end":{"line":3,"column":93}}}) : helper)))
    + "'>\n            <div class='heading'>\n                <h3>\n                  <span class='http_method'>\n                    <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"resourceName") || (depth0 != null ? lookupProperty(depth0,"resourceName") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"resourceName","hash":{},"data":data,"loc":{"start":{"line":7,"column":32},"end":{"line":7,"column":48}}}) : helper)))
    + "/"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":7,"column":49},"end":{"line":7,"column":61}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"httpMethod") || (depth0 != null ? lookupProperty(depth0,"httpMethod") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"httpMethod","hash":{},"data":data,"loc":{"start":{"line":7,"column":62},"end":{"line":7,"column":76}}}) : helper)))
    + "' class=\"toggleOperation\">"
    + alias4(((helper = (helper = lookupProperty(helpers,"httpMethod") || (depth0 != null ? lookupProperty(depth0,"httpMethod") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"httpMethod","hash":{},"data":data,"loc":{"start":{"line":7,"column":102},"end":{"line":7,"column":116}}}) : helper)))
    + "</a>\n                  </span>\n                  <span class='path'>\n                    <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"resourceName") || (depth0 != null ? lookupProperty(depth0,"resourceName") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"resourceName","hash":{},"data":data,"loc":{"start":{"line":10,"column":32},"end":{"line":10,"column":48}}}) : helper)))
    + "/"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":10,"column":49},"end":{"line":10,"column":61}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"httpMethod") || (depth0 != null ? lookupProperty(depth0,"httpMethod") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"httpMethod","hash":{},"data":data,"loc":{"start":{"line":10,"column":62},"end":{"line":10,"column":76}}}) : helper)))
    + "' class=\"toggleOperation\">"
    + alias4(((helper = (helper = lookupProperty(helpers,"path") || (depth0 != null ? lookupProperty(depth0,"path") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"path","hash":{},"data":data,"loc":{"start":{"line":10,"column":102},"end":{"line":10,"column":110}}}) : helper)))
    + "</a>\n                  </span>\n                </h3>\n                <ul class='options'>\n                    <li>\n                        <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"resourceName") || (depth0 != null ? lookupProperty(depth0,"resourceName") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"resourceName","hash":{},"data":data,"loc":{"start":{"line":15,"column":36},"end":{"line":15,"column":52}}}) : helper)))
    + "/"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":15,"column":53},"end":{"line":15,"column":65}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"httpMethod") || (depth0 != null ? lookupProperty(depth0,"httpMethod") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"httpMethod","hash":{},"data":data,"loc":{"start":{"line":15,"column":66},"end":{"line":15,"column":80}}}) : helper)))
    + "' class=\"toggleOperation\">"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"summary") || (depth0 != null ? lookupProperty(depth0,"summary") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"summary","hash":{},"data":data,"loc":{"start":{"line":15,"column":106},"end":{"line":15,"column":119}}}) : helper))) != null ? stack1 : "")
    + "</a>\n                    </li>\n                </ul>\n            </div>\n            <div class='content' id='"
    + alias4(((helper = (helper = lookupProperty(helpers,"resourceName") || (depth0 != null ? lookupProperty(depth0,"resourceName") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"resourceName","hash":{},"data":data,"loc":{"start":{"line":19,"column":37},"end":{"line":19,"column":53}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":19,"column":54},"end":{"line":19,"column":66}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"httpMethod") || (depth0 != null ? lookupProperty(depth0,"httpMethod") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"httpMethod","hash":{},"data":data,"loc":{"start":{"line":19,"column":67},"end":{"line":19,"column":81}}}) : helper)))
    + "_content' style='display:none'>\n                "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"notes") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":20,"column":16},"end":{"line":23,"column":23}}})) != null ? stack1 : "")
    + "\n                "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"responseClass") : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":24,"column":16},"end":{"line":31,"column":23}}})) != null ? stack1 : "")
    + "\n                <form accept-charset='UTF-8' class='sandbox'>\n                    <div style='margin:0;padding:0;display:inline'></div>\n                    <h4>Parameters</h4>\n                    <table class='fullwidth'>\n                        <thead>\n                        <tr>\n                            <th>Parameter</th>\n                            <th>Value</th>\n                            <th style=\"width:50%\">Description</th>\n                        </tr>\n                        </thead>\n                        <tbody class=\"operation-params\">\n\n                        </tbody>\n                    </table>\n                    "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isReadOnly") : depth0),{"name":"if","hash":{},"fn":container.program(4, data, 0),"inverse":container.program(5, data, 0),"data":data,"loc":{"start":{"line":47,"column":20},"end":{"line":54,"column":27}}})) != null ? stack1 : "")
    + "\n                </form>\n                <div class='response' style='display:none'>\n                    <h4>Request URL</h4>\n                    <div class='block request_url'></div>\n                    <h4>Response Body</h4>\n                    <div class='block response_body'></div>\n                    <h4>Response Code</h4>\n                    <div class='block response_code'></div>\n                    <h4>Response Headers</h4>\n                    <div class='block response_headers'></div>\n                </div>\n            </div>\n        </li>\n    </ul>\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['param'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(2, data, 0),"data":data,"loc":{"start":{"line":4,"column":2},"end":{"line":8,"column":9}}})) != null ? stack1 : "")
    + "\n\t\t\n\t";
},"1":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t<textarea class='body-textarea' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":5,"column":41},"end":{"line":5,"column":49}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":5,"column":51},"end":{"line":5,"column":67}}}) : helper)))
    + "</textarea>\n\t\t";
},"2":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t    <textarea class='body-textarea' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":7,"column":44},"end":{"line":7,"column":52}}}) : helper)))
    + "'></textarea>\n\t\t";
},"3":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(4, data, 0),"inverse":container.program(5, data, 0),"data":data,"loc":{"start":{"line":11,"column":2},"end":{"line":15,"column":9}}})) != null ? stack1 : "")
    + "\n\t";
},"4":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t<input minlength='0' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":12,"column":30},"end":{"line":12,"column":38}}}) : helper)))
    + "' placeholder='' type='text' value='"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":12,"column":74},"end":{"line":12,"column":90}}}) : helper)))
    + "'/>\n\t\t";
},"5":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t    <input minlength='0' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":14,"column":33},"end":{"line":14,"column":41}}}) : helper)))
    + "' placeholder='' type='text' value=''/>\n\t\t";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":17},"end":{"line":1,"column":25}}}) : helper)))
    + ": <tag>"
    + alias4(((helper = (helper = lookupProperty(helpers,"dataType") || (depth0 != null ? lookupProperty(depth0,"dataType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"dataType","hash":{},"data":data,"loc":{"start":{"line":1,"column":32},"end":{"line":1,"column":44}}}) : helper)))
    + "</tag></td>\n<td>\n\t"
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isBody") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(3, data, 0),"data":data,"loc":{"start":{"line":3,"column":1},"end":{"line":16,"column":8}}})) != null ? stack1 : "")
    + "\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":18,"column":4},"end":{"line":18,"column":21}}}) : helper))) != null ? stack1 : "")
    + "</td>";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['param_list'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    return "\n        ";
},"1":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n            "
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(2, data, 0),"inverse":container.program(3, data, 0),"data":data,"loc":{"start":{"line":6,"column":12},"end":{"line":9,"column":19}}})) != null ? stack1 : "")
    + "\n        ";
},"2":function(container,depth0,helpers,partials,data) {
    return "\n            ";
},"3":function(container,depth0,helpers,partials,data) {
    return "\n                <option selected=\"\" value=''></option>\n            ";
},"4":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n            "
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"isDefault") : depth0),{"name":"if","hash":{},"fn":container.program(5, data, 0),"inverse":container.program(6, data, 0),"data":data,"loc":{"start":{"line":12,"column":12},"end":{"line":16,"column":19}}})) != null ? stack1 : "")
    + "\n        ";
},"5":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n                <option value='"
    + alias4(((helper = (helper = lookupProperty(helpers,"value") || (depth0 != null ? lookupProperty(depth0,"value") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"value","hash":{},"data":data,"loc":{"start":{"line":13,"column":31},"end":{"line":13,"column":40}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"value") || (depth0 != null ? lookupProperty(depth0,"value") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"value","hash":{},"data":data,"loc":{"start":{"line":13,"column":42},"end":{"line":13,"column":51}}}) : helper)))
    + " (default)</option>\n            ";
},"6":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n                <option value='"
    + alias4(((helper = (helper = lookupProperty(helpers,"value") || (depth0 != null ? lookupProperty(depth0,"value") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"value","hash":{},"data":data,"loc":{"start":{"line":15,"column":31},"end":{"line":15,"column":40}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"value") || (depth0 != null ? lookupProperty(depth0,"value") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"value","hash":{},"data":data,"loc":{"start":{"line":15,"column":42},"end":{"line":15,"column":51}}}) : helper)))
    + "</option>\n            ";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":17},"end":{"line":1,"column":25}}}) : helper)))
    + ": <tag>"
    + alias4(((helper = (helper = lookupProperty(helpers,"dataType") || (depth0 != null ? lookupProperty(depth0,"dataType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"dataType","hash":{},"data":data,"loc":{"start":{"line":1,"column":32},"end":{"line":1,"column":44}}}) : helper)))
    + "</tag></td>\n<td>\n    <select name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":3,"column":18},"end":{"line":3,"column":26}}}) : helper)))
    + "'>\n        "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"required") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(1, data, 0),"data":data,"loc":{"start":{"line":4,"column":8},"end":{"line":10,"column":15}}})) != null ? stack1 : "")
    + "\n        "
    + ((stack1 = lookupProperty(helpers,"each").call(alias1,((stack1 = (depth0 != null ? lookupProperty(depth0,"allowableValues") : depth0)) != null ? lookupProperty(stack1,"descriptiveValues") : stack1),{"name":"each","hash":{},"fn":container.program(4, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":11,"column":8},"end":{"line":17,"column":17}}})) != null ? stack1 : "")
    + "\n    </select>\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":20,"column":4},"end":{"line":20,"column":21}}}) : helper))) != null ? stack1 : "")
    + "</td>";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['param_readonly'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        <textarea class='body-textarea' readonly='readonly' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":4,"column":66},"end":{"line":4,"column":74}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":4,"column":76},"end":{"line":4,"column":92}}}) : helper)))
    + "</textarea>\n    ";
},"1":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        "
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":6,"column":8},"end":{"line":6,"column":24}}}) : helper)))
    + "\n    ";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":17},"end":{"line":1,"column":25}}}) : helper)))
    + ": <tag>"
    + alias4(((helper = (helper = lookupProperty(helpers,"dataType") || (depth0 != null ? lookupProperty(depth0,"dataType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"dataType","hash":{},"data":data,"loc":{"start":{"line":1,"column":32},"end":{"line":1,"column":44}}}) : helper)))
    + "</tag></td>\n<td>\n    "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isBody") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(1, data, 0),"data":data,"loc":{"start":{"line":3,"column":4},"end":{"line":7,"column":11}}})) != null ? stack1 : "")
    + "\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":9,"column":4},"end":{"line":9,"column":21}}}) : helper))) != null ? stack1 : "")
    + "</td>\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['param_readonly_required'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        <textarea class='body-textarea'  readonly='readonly' placeholder='(required)' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":4,"column":92},"end":{"line":4,"column":100}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":4,"column":102},"end":{"line":4,"column":118}}}) : helper)))
    + "</textarea>\n    ";
},"1":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        "
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":6,"column":8},"end":{"line":6,"column":24}}}) : helper)))
    + "\n    ";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code required'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":26},"end":{"line":1,"column":34}}}) : helper)))
    + ": <tag>"
    + alias4(((helper = (helper = lookupProperty(helpers,"dataType") || (depth0 != null ? lookupProperty(depth0,"dataType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"dataType","hash":{},"data":data,"loc":{"start":{"line":1,"column":41},"end":{"line":1,"column":53}}}) : helper)))
    + "</tag></td>\n<td>\n    "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isBody") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(1, data, 0),"data":data,"loc":{"start":{"line":3,"column":4},"end":{"line":7,"column":11}}})) != null ? stack1 : "")
    + "\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":9,"column":4},"end":{"line":9,"column":21}}}) : helper))) != null ? stack1 : "")
    + "</td>";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['param_required'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(2, data, 0),"data":data,"loc":{"start":{"line":4,"column":2},"end":{"line":8,"column":9}}})) != null ? stack1 : "")
    + "\n\t\t\n\t";
},"1":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t<textarea class='body-textarea' placeholder='(required)' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":5,"column":66},"end":{"line":5,"column":74}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":5,"column":76},"end":{"line":5,"column":92}}}) : helper)))
    + "</textarea>\n\t\t";
},"2":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t    <textarea class='body-textarea' placeholder='(required)' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":7,"column":69},"end":{"line":7,"column":77}}}) : helper)))
    + "'></textarea>\n\t\t";
},"3":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(4, data, 0),"inverse":container.program(5, data, 0),"data":data,"loc":{"start":{"line":11,"column":2},"end":{"line":15,"column":9}}})) != null ? stack1 : "")
    + "\n\t";
},"4":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t    <input class='required' minlength='1' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":12,"column":50},"end":{"line":12,"column":58}}}) : helper)))
    + "' placeholder='(required)' type='text' value='"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":12,"column":104},"end":{"line":12,"column":120}}}) : helper)))
    + "'/>\n\t\t";
},"5":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t    <input class='required' minlength='1' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":14,"column":50},"end":{"line":14,"column":58}}}) : helper)))
    + "' placeholder='(required)' type='text' value=''/>\n\t\t";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code required'>"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":26},"end":{"line":1,"column":34}}}) : helper)))
    + ": <tag>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"dataType") || (depth0 != null ? lookupProperty(depth0,"dataType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"dataType","hash":{},"data":data,"loc":{"start":{"line":1,"column":41},"end":{"line":1,"column":55}}}) : helper))) != null ? stack1 : "")
    + "</tag></td>\n<td>\n\t"
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isBody") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(3, data, 0),"data":data,"loc":{"start":{"line":3,"column":1},"end":{"line":16,"column":8}}})) != null ? stack1 : "")
    + "\n</td>\n<td>\n    <strong>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":19,"column":12},"end":{"line":19,"column":29}}}) : helper))) != null ? stack1 : "")
    + "</strong>\n</td>";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['resource'] = Handlebars.template({"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<div class='heading'>\n    <h2>\n        <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":3,"column":20},"end":{"line":3,"column":28}}}) : helper)))
    + "' onclick=\"Docs.toggleEndpointListForResource('"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":3,"column":75},"end":{"line":3,"column":83}}}) : helper)))
    + "');\">/"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":3,"column":89},"end":{"line":3,"column":97}}}) : helper)))
    + "</a>\n    </h2>\n    <ul class='options'>\n        <li>\n            <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":7,"column":24},"end":{"line":7,"column":32}}}) : helper)))
    + "' id='endpointListTogger_"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":7,"column":57},"end":{"line":7,"column":65}}}) : helper)))
    + "'\n               onclick=\"Docs.toggleEndpointListForResource('"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":8,"column":60},"end":{"line":8,"column":68}}}) : helper)))
    + "');\">Show/Hide</a>\n        </li>\n        <li>\n            <a href='#' onclick=\"Docs.collapseOperationsForResource('"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":11,"column":69},"end":{"line":11,"column":77}}}) : helper)))
    + "'); return false;\">\n                List Operations\n            </a>\n        </li>\n        <li>\n            <a href='#' onclick=\"Docs.expandOperationsForResource('"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":16,"column":67},"end":{"line":16,"column":75}}}) : helper)))
    + "'); return false;\">\n                Expand Operations\n            </a>\n        </li>\n        <li>\n            <a href='"
    + alias4(((helper = (helper = lookupProperty(helpers,"url") || (depth0 != null ? lookupProperty(depth0,"url") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"url","hash":{},"data":data,"loc":{"start":{"line":21,"column":21},"end":{"line":21,"column":28}}}) : helper)))
    + "' target=\"_blank\">Raw</a>\n        </li>\n    </ul>\n</div>\n<ul class='endpoints' id='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":25,"column":26},"end":{"line":25,"column":34}}}) : helper)))
    + "_endpoint_list' style='display:none'>\n\n</ul>\n";
},"useData":true});
})();



// Generated by CoffeeScript 1.4.0
(function() {
  var HeaderView, MainView, OperationView, ParameterView, ResourceView, SwaggerUi,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  SwaggerUi = (function(_super) {

    __extends(SwaggerUi, _super);

    function SwaggerUi() {
      return SwaggerUi.__super__.constructor.apply(this, arguments);
    }

    SwaggerUi.prototype.dom_id = "swagger_ui";

    SwaggerUi.prototype.options = null;

    SwaggerUi.prototype.api = null;

    SwaggerUi.prototype.headerView = null;

    SwaggerUi.prototype.mainView = null;

    SwaggerUi.prototype.initialize = function(options) {
      var _this = this;
      if (options == null) {
        options = {};
      }
      if (options.dom_id != null) {
        this.dom_id = options.dom_id;
        delete options.dom_id;
      }
      if (!($('#' + this.dom_id) != null)) {
        $('body').append('<div id="' + this.dom_id + '"></div>');
      }
      this.options = options;
      this.options.success = function() {
        return _this.render();
      };
      this.options.progress = function(d) {
        return _this.showMessage(d);
      };
      this.options.failure = function(d) {
        return _this.onLoadFailure(d);
      };
      this.headerView = new HeaderView({
        el: $('#header')
      });
      return this.headerView.on('update-swagger-ui', function(data) {
        return _this.updateSwaggerUi(data);
      });
    };

    SwaggerUi.prototype.updateSwaggerUi = function(data) {
      this.options.discoveryUrl = data.discoveryUrl;
      this.options.apiKey = data.apiKey;
      return this.load();
    };

    SwaggerUi.prototype.load = function() {
      var _ref;
      if ((_ref = this.mainView) != null) {
        _ref.clear();
      }
      this.headerView.update(this.options.discoveryUrl, this.options.apiKey);
      return this.api = new SwaggerApi(this.options);
    };

    SwaggerUi.prototype.render = function() {
      var _this = this;
      this.showMessage('Finished Loading Resource Information. Rendering Swagger UI...');
      this.mainView = new MainView({
        model: this.api,
        el: $('#' + this.dom_id)
      }).render();
      this.showMessage();
      switch (this.options.docExpansion) {
        case "full":
          Docs.expandOperationsForResource('');
          break;
        case "list":
          Docs.collapseOperationsForResource('');
      }
      if (this.options.onComplete) {
        this.options.onComplete(this.api, this);
      }
      return setTimeout(function() {
        return Docs.shebang();
      }, 400);
    };

    SwaggerUi.prototype.showMessage = function(data) {
      if (data == null) {
        data = '';
      }
      $('#message-bar').removeClass('message-fail');
      $('#message-bar').addClass('message-success');
      return $('#message-bar').html(data);
    };

    SwaggerUi.prototype.onLoadFailure = function(data) {
      var val;
      if (data == null) {
        data = '';
      }
      $('#message-bar').removeClass('message-success');
      $('#message-bar').addClass('message-fail');
      val = $('#message-bar').html(data);
      if (this.options.onFailure != null) {
        this.options.onFailure(data);
      }
      return val;
    };

    return SwaggerUi;

  })(Backbone.Router);

  window.SwaggerUi = SwaggerUi;

  HeaderView = (function(_super) {

    __extends(HeaderView, _super);

    function HeaderView() {
      return HeaderView.__super__.constructor.apply(this, arguments);
    }

    HeaderView.prototype.events = {
      'click #show-pet-store-icon': 'showPetStore',
      'click #show-wordnik-dev-icon': 'showWordnikDev',
      'click #explore': 'showCustom',
      'keyup #input_baseUrl': 'showCustomOnKeyup',
      'keyup #input_apiKey': 'showCustomOnKeyup'
    };

    HeaderView.prototype.initialize = function() {};

    HeaderView.prototype.showPetStore = function(e) {
      return this.trigger('update-swagger-ui', {
        discoveryUrl: "http://petstore.swagger.wordnik.com/api/resources.json",
        apiKey: "special-key"
      });
    };

    HeaderView.prototype.showWordnikDev = function(e) {
      return this.trigger('update-swagger-ui', {
        discoveryUrl: "http://api.wordnik.com/v4/resources.json",
        apiKey: ""
      });
    };

    HeaderView.prototype.showCustomOnKeyup = function(e) {
      if (e.keyCode === 13) {
        return this.showCustom();
      }
    };

    HeaderView.prototype.showCustom = function(e) {
      if (e != null) {
        e.preventDefault();
      }
      return this.trigger('update-swagger-ui', {
        discoveryUrl: $('#input_baseUrl').val(),
        apiKey: $('#input_apiKey').val()
      });
    };

    HeaderView.prototype.update = function(url, apiKey, trigger) {
      if (trigger == null) {
        trigger = false;
      }
      $('#input_baseUrl').val(url);
      $('#input_apiKey').val(apiKey);
      if (trigger) {
        return this.trigger('update-swagger-ui', {
          discoveryUrl: url,
          apiKey: apiKey
        });
      }
    };

    return HeaderView;

  })(Backbone.View);

  MainView = (function(_super) {

    __extends(MainView, _super);

    function MainView() {
      return MainView.__super__.constructor.apply(this, arguments);
    }

    MainView.prototype.initialize = function() {};

    MainView.prototype.render = function() {
      var resource, _i, _len, _ref;
      $(this.el).html(Handlebars.templates.main(this.model));
      _ref = this.model.resourcesArray;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        resource = _ref[_i];
        this.addResource(resource);
      }
      return this;
    };

    MainView.prototype.addResource = function(resource) {
      var resourceView;
      resourceView = new ResourceView({
        model: resource,
        tagName: 'li',
        id: 'resource_' + resource.name,
        className: 'resource'
      });
      return $('#resources').append(resourceView.render().el);
    };

    MainView.prototype.clear = function() {
      return $(this.el).html('');
    };

    return MainView;

  })(Backbone.View);

  ResourceView = (function(_super) {

    __extends(ResourceView, _super);

    function ResourceView() {
      return ResourceView.__super__.constructor.apply(this, arguments);
    }

    ResourceView.prototype.initialize = function() {};

    ResourceView.prototype.render = function() {
      var operation, _i, _len, _ref;
      $(this.el).html(Handlebars.templates.resource(this.model));
      _ref = this.model.operationsArray;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        operation = _ref[_i];
        this.addOperation(operation);
      }
      return this;
    };

    ResourceView.prototype.addOperation = function(operation) {
      var operationView;
      operationView = new OperationView({
        model: operation,
        tagName: 'li',
        className: 'endpoint'
      });
      return $('.endpoints', $(this.el)).append(operationView.render().el);
    };

    return ResourceView;

  })(Backbone.View);

  OperationView = (function(_super) {

    __extends(OperationView, _super);

    function OperationView() {
      return OperationView.__super__.constructor.apply(this, arguments);
    }

    OperationView.prototype.events = {
      'submit .sandbox': 'submitOperation',
      'click .submit': 'submitOperation',
      'click .response_hider': 'hideResponse',
      'click .toggleOperation': 'toggleOperationContent'
    };

    OperationView.prototype.initialize = function() {};

    OperationView.prototype.render = function() {
      var isMethodSubmissionSupported, param, _i, _len, _ref;
      isMethodSubmissionSupported = jQuery.inArray(this.model.httpMethod, this.model.supportedSubmitMethods()) >= 0;
      if (!isMethodSubmissionSupported) {
        this.model.isReadOnly = true;
      }
      $(this.el).html(Handlebars.templates.operation(this.model));
      _ref = this.model.parameters;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        param = _ref[_i];
        this.addParameter(param);
      }
      return this;
    };

    OperationView.prototype.addParameter = function(param) {
      var paramView;
      paramView = new ParameterView({
        model: param,
        tagName: 'tr',
        readOnly: this.model.isReadOnly
      });
      return $('.operation-params', $(this.el)).append(paramView.render().el);
    };

    OperationView.prototype.submitOperation = function(e) {
      var bodyParam, error_free, form, headerParams, invocationUrl, map, o, obj, param, _i, _j, _len, _len1, _ref, _ref1,
        _this = this;
      if (e != null) {
        e.preventDefault();
      }
      form = $('.sandbox', $(this.el));
      error_free = true;
      form.find("input.required").each(function() {
        var _this = this;
        $(this).removeClass("error");
        if (jQuery.trim($(this).val()) === "") {
          $(this).addClass("error");
          $(this).wiggle({
            callback: function() {
              return $(_this).focus();
            }
          });
          return error_free = false;
        }
      });
      if (error_free) {
        map = {};
        _ref = form.serializeArray();
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          o = _ref[_i];
          if ((o.value != null) && jQuery.trim(o.value).length > 0) {
            map[o.name] = o.value;
          }
        }
        bodyParam = null;
        _ref1 = this.model.parameters;
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          param = _ref1[_j];
          if (param.paramType === 'body') {
            bodyParam = map[param.name];
          }
        }
        log("bodyParam = " + bodyParam);
        headerParams = null;
        invocationUrl = this.model.supportHeaderParams() ? (headerParams = this.model.getHeaderParams(map), this.model.urlify(map, false)) : this.model.urlify(map, true);
        log('submitting ' + invocationUrl);
        $(".request_url", $(this.el)).html("<pre>" + invocationUrl + "</pre>");
        $(".response_throbber", $(this.el)).show();
        obj = {
          type: this.model.httpMethod,
          url: invocationUrl,
          headers: headerParams,
          data: bodyParam,
          dataType: 'json',
          error: function(xhr, textStatus, error) {
            return _this.showErrorStatus(xhr, textStatus, error);
          },
          success: function(data) {
            return _this.showResponse(data);
          },
          complete: function(data) {
            return _this.showCompleteStatus(data);
          }
        };
        if (obj.type.toLowerCase() === "post" || obj.type.toLowerCase() === "put" || obj.type.toLowerCase() === "patch") {
          obj.contentType = "application/json";
        }
        jQuery.ajax(obj);
        return false;
      }
    };

    OperationView.prototype.hideResponse = function(e) {
      if (e != null) {
        e.preventDefault();
      }
      $(".response", $(this.el)).slideUp();
      return $(".response_hider", $(this.el)).fadeOut();
    };

    OperationView.prototype.showResponse = function(response) {
      var prettyJson;
      prettyJson = JSON.stringify(response, null, "\t").replace(/\n/g, "<br>");
      return $(".response_body", $(this.el)).html(prettyJson);
    };

    OperationView.prototype.showErrorStatus = function(data) {
      return this.showStatus(data);
    };

    OperationView.prototype.showCompleteStatus = function(data) {
      return this.showStatus(data);
    };

    OperationView.prototype.showStatus = function(data) {
      var response_body;
      try {
        if (data.responseText.length > 0) {
          response_body = "<pre>" + JSON.stringify(JSON.parse(data.responseText), null, 2).replace(/\n/g, "<br>") + "</pre>";
        } else {
          response_body = "<pre style='color:dimgrey'>[No Content]</pre>";
        }
      } catch (error) {
        response_body = "<span style='color:red'>&nbsp;&nbsp;&nbsp;[unable to parse as json; raw response below]</span><br><pre>" + data.responseText + "</pre>";
      }
      $(".response_code", $(this.el)).html("<pre>" + data.status + ' ' + data.statusText + "</pre>");
      $(".response_body", $(this.el)).html(response_body);
      $(".response_headers", $(this.el)).html("<pre>" + data.getAllResponseHeaders() + "</pre>");
      $(".response", $(this.el)).slideDown();
      $(".response_hider", $(this.el)).show();
      return $(".response_throbber", $(this.el)).hide();
    };

    OperationView.prototype.toggleOperationContent = function() {
      var elem;
      elem = $('#' + this.model.resourceName + "_" + this.model.nickname + "_" + this.model.httpMethod + "_content");
      if (elem.is(':visible')) {
        return Docs.collapseOperation(elem);
      } else {
        return Docs.expandOperation(elem);
      }
    };

    return OperationView;

  })(Backbone.View);

  ParameterView = (function(_super) {

    __extends(ParameterView, _super);

    function ParameterView() {
      return ParameterView.__super__.constructor.apply(this, arguments);
    }

    ParameterView.prototype.initialize = function() {};

    ParameterView.prototype.render = function() {
      var template;
      if (this.model.paramType === 'body') {
        this.model.isBody = true;
      }
      template = this.template();
      $(this.el).html(template(this.model));
      return this;
    };

    ParameterView.prototype.template = function() {
      if (this.model.isList) {
        return Handlebars.templates.param_list;
      } else {
        if (this.options.readOnly) {
          if (this.model.required) {
            return Handlebars.templates.param_readonly_required;
          } else {
            return Handlebars.templates.param_readonly;
          }
        } else {
          if (this.model.required) {
            return Handlebars.templates.param_required;
          } else {
            return Handlebars.templates.param;
          }
        }
      }
    };

    return ParameterView;

  })(Backbone.View);

}).call(this);
