// swagger-ui.js
// version 2.0.17
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
log = function(){
  log.history = log.history || [];
  log.history.push(arguments);
  if(this.console){
    console.log( Array.prototype.slice.call(arguments) );
  }
};

// Handle browsers that do console incorrectly (IE9 and below, see http://stackoverflow.com/a/5539378/7913)
if (Function.prototype.bind && console && typeof console.log == "object") {
    [
      "log","info","warn","error","assert","dir","clear","profile","profileEnd"
    ].forEach(function (method) {
        console[method] = this.bind(console[method], console);
    }, Function.prototype.call);
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
				log('shebang resource:' + fragments[0]);
				var dom_id = 'resource_' + fragments[0];

				Docs.expandEndpointListForResource(fragments[0]);
				$("#"+dom_id).slideto({highlight: false});
				break;
			case 2:
				// Refer to the endpoint DOM element, e.g. #words_get_search
				log('shebang endpoint: ' + fragments.join('_'));

        // Expand Resource
        Docs.expandEndpointListForResource(fragments[0]);
        $("#"+dom_id).slideto({highlight: false});

        // Expand operation
				var li_dom_id = fragments.join('_');
				var li_content_dom_id = li_dom_id + "_content";

        log("li_dom_id " + li_dom_id);
        log("li_content_dom_id " + li_content_dom_id);

				Docs.expandOperation($('#'+li_content_dom_id));
				$('#'+li_dom_id).slideto({highlight: false});
				break;
		}

	},

	toggleEndpointListForResource: function(resource) {
		var elem = $('li#resource_' + Docs.escapeResourceName(resource) + ' ul.endpoints');
		if (elem.is(':visible')) {
			Docs.collapseEndpointListForResource(resource);
		} else {
			Docs.expandEndpointListForResource(resource);
		}
	},

	// Expand resource
	expandEndpointListForResource: function(resource) {
		var resource = Docs.escapeResourceName(resource);
		if (resource == '') {
			$('.resource ul.endpoints').slideDown();
			return;
		}
		
		$('li#resource_' + resource).addClass('active');

		var elem = $('li#resource_' + resource + ' ul.endpoints');
		elem.slideDown();
	},

	// Collapse resource and mark as explicitly closed
	collapseEndpointListForResource: function(resource) {
		var resource = Docs.escapeResourceName(resource);
		$('li#resource_' + resource).removeClass('active');

		var elem = $('li#resource_' + resource + ' ul.endpoints');
		elem.slideUp();
	},

	expandOperationsForResource: function(resource) {
		// Make sure the resource container is open..
		Docs.expandEndpointListForResource(resource);
		
		if (resource == '') {
			$('.resource ul.endpoints li.operation div.content').slideDown();
			return;
		}

		$('li#resource_' + Docs.escapeResourceName(resource) + ' li.operation div.content').each(function() {
			Docs.expandOperation($(this));
		});
	},

	collapseOperationsForResource: function(resource) {
		// Make sure the resource container is open..
		Docs.expandEndpointListForResource(resource);

		$('li#resource_' + Docs.escapeResourceName(resource) + ' li.operation div.content').each(function() {
			Docs.collapseOperation($(this));
		});
	},

	escapeResourceName: function(resource) {
		return resource.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]\^`{|}~]/g, "\\$&");
	},

	expandOperation: function(elem) {
		elem.slideDown();
	},

	collapseOperation: function(elem) {
		elem.slideUp();
	}
};(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['content_type'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n  "
    + ((stack1 = lookupProperty(helpers,"each").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"produces") : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":4,"column":2},"end":{"line":6,"column":10}}})) != null ? stack1 : "")
    + "\n";
},"1":function(container,depth0,helpers,partials,data) {
    var stack1, alias1=container.lambda;

  return "\n\t<option value=\""
    + ((stack1 = alias1(depth0, depth0)) != null ? stack1 : "")
    + "\">"
    + ((stack1 = alias1(depth0, depth0)) != null ? stack1 : "")
    + "</option>\n\t";
},"2":function(container,depth0,helpers,partials,data) {
    return "\n  <option value=\"application/json\">application/json</option>\n";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<label for=\"contentType\"></label>\n<select name=\"contentType\">\n"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"produces") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(2, data, 0),"data":data,"loc":{"start":{"line":3,"column":0},"end":{"line":9,"column":7}}})) != null ? stack1 : "")
    + "\n</select>\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['main'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var stack1, alias1=depth0 != null ? depth0 : (container.nullContext || {}), lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n                "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,((stack1 = (depth0 != null ? lookupProperty(depth0,"info") : depth0)) != null ? lookupProperty(stack1,"termsOfServiceUrl") : stack1),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":10,"column":16},"end":{"line":10,"column":170}}})) != null ? stack1 : "")
    + "\n                "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,((stack1 = (depth0 != null ? lookupProperty(depth0,"info") : depth0)) != null ? lookupProperty(stack1,"contact") : stack1),{"name":"if","hash":{},"fn":container.program(2, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":11,"column":16},"end":{"line":11,"column":130}}})) != null ? stack1 : "")
    + "\n                "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,((stack1 = (depth0 != null ? lookupProperty(depth0,"info") : depth0)) != null ? lookupProperty(stack1,"license") : stack1),{"name":"if","hash":{},"fn":container.program(3, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":12,"column":16},"end":{"line":12,"column":158}}})) != null ? stack1 : "")
    + "\n            ";
},"1":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<li><a href=\""
    + container.escapeExpression(container.lambda(((stack1 = (depth0 != null ? lookupProperty(depth0,"info") : depth0)) != null ? lookupProperty(stack1,"termsOfServiceUrl") : stack1), depth0))
    + "\" title=\"Terms of Service\"><i class=\"fa fa-lg fa-bullhorn\"></i> Terms</a></li>";
},"2":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<li><a href=\"mailto:"
    + container.escapeExpression(container.lambda(((stack1 = (depth0 != null ? lookupProperty(depth0,"info") : depth0)) != null ? lookupProperty(stack1,"contact") : stack1), depth0))
    + "\"><i class=\"fa fa-lg fa-send\"></i> Contact</a></li>";
},"3":function(container,depth0,helpers,partials,data) {
    var stack1, alias1=container.lambda, alias2=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<li><a href=\""
    + alias2(alias1(((stack1 = (depth0 != null ? lookupProperty(depth0,"info") : depth0)) != null ? lookupProperty(stack1,"licenseUrl") : stack1), depth0))
    + "\" title=\""
    + alias2(alias1(((stack1 = (depth0 != null ? lookupProperty(depth0,"info") : depth0)) != null ? lookupProperty(stack1,"license") : stack1), depth0))
    + "\"><i class=\"fa fa-lg fa-certificate\"></i> License</a></li>";
},"4":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return " v"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"apiVersion") || (depth0 != null ? lookupProperty(depth0,"apiVersion") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"apiVersion","hash":{},"data":data,"loc":{"start":{"line":15,"column":48},"end":{"line":15,"column":62}}}) : helper)));
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<div class='info' id='api_info'>\n</div>\n<div class='container' id='resources_container'>\n    <ul id='resources'>\n    </ul>\n\n    <div class=\"footer\">\n        <ul id=\"footer-nav\">\n            "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"info") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":9,"column":12},"end":{"line":13,"column":19}}})) != null ? stack1 : "")
    + "\n            <li>\n                "
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"basePath") || (depth0 != null ? lookupProperty(depth0,"basePath") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(alias1,{"name":"basePath","hash":{},"data":data,"loc":{"start":{"line":15,"column":16},"end":{"line":15,"column":28}}}) : helper)))
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"apiVersion") : depth0),{"name":"if","hash":{},"fn":container.program(4, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":15,"column":28},"end":{"line":15,"column":69}}})) != null ? stack1 : "")
    + " Powered by Restler & Swagger\n            </li>\n        </ul>\n    </div>\n</div>\n";
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

  return "\n        <h4>Implementation Notes</h4>\n        <p>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"notes") || (depth0 != null ? lookupProperty(depth0,"notes") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"notes","hash":{},"data":data,"loc":{"start":{"line":22,"column":11},"end":{"line":22,"column":22}}}) : helper))) != null ? stack1 : "")
    + "</p>\n        ";
},"1":function(container,depth0,helpers,partials,data) {
    return "\n        <div class=\"auth\">\n        <span class=\"api-ic ic-error\"></span>";
},"2":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n          <div id=\"api_information_panel\" style=\"top: 526px; left: 776px; display: none;\">\n          "
    + ((stack1 = lookupProperty(helpers,"each").call(depth0 != null ? depth0 : (container.nullContext || {}),depth0,{"name":"each","hash":{},"fn":container.program(3, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":29,"column":10},"end":{"line":31,"column":19}}})) != null ? stack1 : "")
    + "\n          </div>\n        ";
},"3":function(container,depth0,helpers,partials,data) {
    var stack1, alias1=container.lambda, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n            <div title='"
    + ((stack1 = alias1((depth0 != null ? lookupProperty(depth0,"description") : depth0), depth0)) != null ? stack1 : "")
    + "'>"
    + container.escapeExpression(alias1((depth0 != null ? lookupProperty(depth0,"scope") : depth0), depth0))
    + "</div>\n          ";
},"4":function(container,depth0,helpers,partials,data) {
    return "</div>";
},"5":function(container,depth0,helpers,partials,data) {
    return "\n        <div class='access'>\n          <span class=\"api-ic ic-off\" title=\"click to authenticate\"></span>\n        </div>\n        ";
},"6":function(container,depth0,helpers,partials,data) {
    return "\n          <h4>Response Class</h4>\n          <p><span class=\"model-signature\" /></p>\n          <br/>\n          <div class=\"response-content-type\" />\n        ";
},"7":function(container,depth0,helpers,partials,data) {
    return "\n          <h4>Parameters</h4>\n          <table class='fullwidth'>\n          <thead>\n            <tr>\n            <th style=\"width: 100px; max-width: 100px\">Parameter</th>\n            <th style=\"width: 310px; max-width: 310px\">Value</th>\n            <th style=\"width: 200px; max-width: 200px\">Description</th>\n            <th style=\"width: 100px; max-width: 100px\">Parameter Type</th>\n            <th style=\"width: 220px; max-width: 230px\">Data Type</th>\n            </tr>\n          </thead>\n          <tbody class=\"operation-params\">\n\n          </tbody>\n          </table>\n          ";
},"8":function(container,depth0,helpers,partials,data) {
    return "\n          <div style='margin:0;padding:0;display:inline'></div>\n          <h4>Response Messages</h4>\n          <table class='fullwidth'>\n            <thead>\n            <tr>\n              <th>HTTP Status Code</th>\n              <th>Reason</th>\n              <th>Response Model</th>\n            </tr>\n            </thead>\n            <tbody class=\"operation-status\">\n            \n            </tbody>\n          </table>\n          ";
},"9":function(container,depth0,helpers,partials,data) {
    return "\n          ";
},"10":function(container,depth0,helpers,partials,data) {
    return "\n          <div class='sandbox_header'>\n            <input class='submit' name='commit' type='button' value='Try it out!' />\n            <a href='#' class='response_hider' style='display:none'>Hide Response</a>\n            <img alt='Throbber' class='response_throbber' src='images/throbber.gif' style='display:none' />\n          </div>\n          ";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, options, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, alias5=container.hooks.blockHelperMissing, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    }, buffer =
  "\n  <ul class='operations' >\n    <li class='"
    + alias4(((helper = (helper = lookupProperty(helpers,"method") || (depth0 != null ? lookupProperty(depth0,"method") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"method","hash":{},"data":data,"loc":{"start":{"line":3,"column":15},"end":{"line":3,"column":25}}}) : helper)))
    + " operation' id='"
    + alias4(((helper = (helper = lookupProperty(helpers,"parentId") || (depth0 != null ? lookupProperty(depth0,"parentId") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"parentId","hash":{},"data":data,"loc":{"start":{"line":3,"column":41},"end":{"line":3,"column":53}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":3,"column":54},"end":{"line":3,"column":66}}}) : helper)))
    + "'>\n      <div class='heading'>\n        <h3>\n          <span class='http_method'>\n          <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"parentId") || (depth0 != null ? lookupProperty(depth0,"parentId") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"parentId","hash":{},"data":data,"loc":{"start":{"line":7,"column":22},"end":{"line":7,"column":34}}}) : helper)))
    + "/"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":7,"column":35},"end":{"line":7,"column":47}}}) : helper)))
    + "' class=\"toggleOperation\">"
    + alias4(((helper = (helper = lookupProperty(helpers,"method") || (depth0 != null ? lookupProperty(depth0,"method") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"method","hash":{},"data":data,"loc":{"start":{"line":7,"column":73},"end":{"line":7,"column":83}}}) : helper)))
    + "</a>\n          </span>\n          <span class='path'>\n          <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"parentId") || (depth0 != null ? lookupProperty(depth0,"parentId") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"parentId","hash":{},"data":data,"loc":{"start":{"line":10,"column":22},"end":{"line":10,"column":34}}}) : helper)))
    + "/"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":10,"column":35},"end":{"line":10,"column":47}}}) : helper)))
    + "' class=\"toggleOperation\">"
    + alias4(((helper = (helper = lookupProperty(helpers,"path") || (depth0 != null ? lookupProperty(depth0,"path") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"path","hash":{},"data":data,"loc":{"start":{"line":10,"column":73},"end":{"line":10,"column":81}}}) : helper)))
    + "</a>\n          </span>\n        </h3>\n        <ul class='options'>\n          <li>\n          <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"parentId") || (depth0 != null ? lookupProperty(depth0,"parentId") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"parentId","hash":{},"data":data,"loc":{"start":{"line":15,"column":22},"end":{"line":15,"column":34}}}) : helper)))
    + "/"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":15,"column":35},"end":{"line":15,"column":47}}}) : helper)))
    + "' class=\"toggleOperation\">"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"summary") || (depth0 != null ? lookupProperty(depth0,"summary") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"summary","hash":{},"data":data,"loc":{"start":{"line":15,"column":73},"end":{"line":15,"column":86}}}) : helper))) != null ? stack1 : "")
    + "</a>\n          </li>\n        </ul>\n      </div>\n      <div class='content' id='"
    + alias4(((helper = (helper = lookupProperty(helpers,"parentId") || (depth0 != null ? lookupProperty(depth0,"parentId") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"parentId","hash":{},"data":data,"loc":{"start":{"line":19,"column":31},"end":{"line":19,"column":43}}}) : helper)))
    + "_"
    + alias4(((helper = (helper = lookupProperty(helpers,"nickname") || (depth0 != null ? lookupProperty(depth0,"nickname") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"nickname","hash":{},"data":data,"loc":{"start":{"line":19,"column":44},"end":{"line":19,"column":56}}}) : helper)))
    + "_content' style='display:none'>\n        "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"notes") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":20,"column":8},"end":{"line":23,"column":15}}})) != null ? stack1 : "")
    + "\n        ";
  stack1 = ((helper = (helper = lookupProperty(helpers,"oauth") || (depth0 != null ? lookupProperty(depth0,"oauth") : depth0)) != null ? helper : alias2),(options={"name":"oauth","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":24,"column":8},"end":{"line":26,"column":55}}}),(typeof helper === alias3 ? helper.call(alias1,options) : helper));
  if (!lookupProperty(helpers,"oauth")) { stack1 = alias5.call(depth0,stack1,options)}
  if (stack1 != null) { buffer += stack1; }
  buffer += "\n        "
    + ((stack1 = lookupProperty(helpers,"each").call(alias1,(depth0 != null ? lookupProperty(depth0,"oauth") : depth0),{"name":"each","hash":{},"fn":container.program(2, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":27,"column":8},"end":{"line":33,"column":17}}})) != null ? stack1 : "")
    + "\n        ";
  stack1 = ((helper = (helper = lookupProperty(helpers,"oauth") || (depth0 != null ? lookupProperty(depth0,"oauth") : depth0)) != null ? helper : alias2),(options={"name":"oauth","hash":{},"fn":container.program(4, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":34,"column":8},"end":{"line":34,"column":34}}}),(typeof helper === alias3 ? helper.call(alias1,options) : helper));
  if (!lookupProperty(helpers,"oauth")) { stack1 = alias5.call(depth0,stack1,options)}
  if (stack1 != null) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = ((helper = (helper = lookupProperty(helpers,"oauth") || (depth0 != null ? lookupProperty(depth0,"oauth") : depth0)) != null ? helper : alias2),(options={"name":"oauth","hash":{},"fn":container.program(5, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":35,"column":8},"end":{"line":39,"column":18}}}),(typeof helper === alias3 ? helper.call(alias1,options) : helper));
  if (!lookupProperty(helpers,"oauth")) { stack1 = alias5.call(depth0,stack1,options)}
  if (stack1 != null) { buffer += stack1; }
  return buffer + "\n        "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"type") : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":40,"column":8},"end":{"line":45,"column":15}}})) != null ? stack1 : "")
    + "\n        <form accept-charset='UTF-8' class='sandbox'>\n          <div style='margin:0;padding:0;display:inline'></div>\n          "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"parameters") : depth0),{"name":"if","hash":{},"fn":container.program(7, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":48,"column":10},"end":{"line":64,"column":17}}})) != null ? stack1 : "")
    + "\n          "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"responseMessages") : depth0),{"name":"if","hash":{},"fn":container.program(8, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":65,"column":10},"end":{"line":80,"column":17}}})) != null ? stack1 : "")
    + "\n          "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isReadOnly") : depth0),{"name":"if","hash":{},"fn":container.program(9, data, 0),"inverse":container.program(10, data, 0),"data":data,"loc":{"start":{"line":81,"column":10},"end":{"line":88,"column":17}}})) != null ? stack1 : "")
    + "\n        </form>\n        <div class='response' style='display:none'>\n          <h4>Request URL</h4>\n          <div class='block request_url'></div>\n          <h4>Response Body</h4>\n          <div class='block response_body'></div>\n          <h4>Response Code</h4>\n          <div class='block response_code'></div>\n          <h4>Response Headers</h4>\n          <div class='block response_headers'></div>\n        </div>\n      </div>\n    </li>\n  </ul>\n";
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
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"isFile") : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(2, data, 0),"data":data,"loc":{"start":{"line":5,"column":2},"end":{"line":16,"column":9}}})) != null ? stack1 : "")
    + "\n\t";
},"1":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t<input type=\"file\" name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":6,"column":28},"end":{"line":6,"column":36}}}) : helper)))
    + "'/>\n\t\t\t<div class=\"parameter-content-type\" />\n\t\t";
},"2":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(3, data, 0),"inverse":container.program(4, data, 0),"data":data,"loc":{"start":{"line":9,"column":3},"end":{"line":15,"column":10}}})) != null ? stack1 : "")
    + "\n\t\t";
},"3":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t\t<textarea class='body-textarea' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":10,"column":42},"end":{"line":10,"column":50}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":10,"column":52},"end":{"line":10,"column":68}}}) : helper)))
    + "</textarea>\n\t\t\t";
},"4":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t\t<textarea class='body-textarea' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":12,"column":42},"end":{"line":12,"column":50}}}) : helper)))
    + "'></textarea>\n\t\t\t\t<br />\n\t\t\t\t<div class=\"parameter-content-type\" />\n\t\t\t";
},"5":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.program(7, data, 0),"data":data,"loc":{"start":{"line":18,"column":2},"end":{"line":22,"column":9}}})) != null ? stack1 : "")
    + "\n\t";
},"6":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t<input class='parameter' minlength='0' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":19,"column":48},"end":{"line":19,"column":56}}}) : helper)))
    + "' placeholder='' type='text' value='"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":19,"column":92},"end":{"line":19,"column":108}}}) : helper)))
    + "'/>\n\t\t";
},"7":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t<input class='parameter' minlength='0' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":21,"column":48},"end":{"line":21,"column":56}}}) : helper)))
    + "' placeholder='' type='text' value=''/>\n\t\t";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code'>"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":17},"end":{"line":1,"column":25}}}) : helper)))
    + "</td>\n<td>\n\n\t"
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isBody") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(5, data, 0),"data":data,"loc":{"start":{"line":4,"column":1},"end":{"line":23,"column":8}}})) != null ? stack1 : "")
    + "\n\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":26,"column":4},"end":{"line":26,"column":21}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"paramType") || (depth0 != null ? lookupProperty(depth0,"paramType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"paramType","hash":{},"data":data,"loc":{"start":{"line":27,"column":4},"end":{"line":27,"column":19}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td>\n\t<span class=\"model-signature\"></span>\n</td>\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['param_list'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    return " multiple='multiple'";
},"1":function(container,depth0,helpers,partials,data) {
    return "\n    ";
},"2":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n      "
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(3, data, 0),"inverse":container.program(4, data, 0),"data":data,"loc":{"start":{"line":6,"column":6},"end":{"line":12,"column":13}}})) != null ? stack1 : "")
    + "\n    ";
},"3":function(container,depth0,helpers,partials,data) {
    return "\n      ";
},"4":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        "
    + ((stack1 = (lookupProperty(helpers,"isArray")||(depth0 && lookupProperty(depth0,"isArray"))||container.hooks.helperMissing).call(depth0 != null ? depth0 : (container.nullContext || {}),depth0,{"name":"isArray","hash":{},"fn":container.program(5, data, 0),"inverse":container.program(6, data, 0),"data":data,"loc":{"start":{"line":8,"column":8},"end":{"line":11,"column":20}}})) != null ? stack1 : "")
    + "\n      ";
},"5":function(container,depth0,helpers,partials,data) {
    return "\n        ";
},"6":function(container,depth0,helpers,partials,data) {
    return "\n          <option selected=\"\" value=''></option>\n        ";
},"7":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n      "
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"isDefault") : depth0),{"name":"if","hash":{},"fn":container.program(8, data, 0),"inverse":container.program(9, data, 0),"data":data,"loc":{"start":{"line":15,"column":6},"end":{"line":19,"column":13}}})) != null ? stack1 : "")
    + "\n    ";
},"8":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        <option selected=\"\" value='"
    + alias4(((helper = (helper = lookupProperty(helpers,"value") || (depth0 != null ? lookupProperty(depth0,"value") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"value","hash":{},"data":data,"loc":{"start":{"line":16,"column":35},"end":{"line":16,"column":44}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"value") || (depth0 != null ? lookupProperty(depth0,"value") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"value","hash":{},"data":data,"loc":{"start":{"line":16,"column":46},"end":{"line":16,"column":55}}}) : helper)))
    + " (default)</option>\n      ";
},"9":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        <option value='"
    + alias4(((helper = (helper = lookupProperty(helpers,"value") || (depth0 != null ? lookupProperty(depth0,"value") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"value","hash":{},"data":data,"loc":{"start":{"line":18,"column":23},"end":{"line":18,"column":32}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"value") || (depth0 != null ? lookupProperty(depth0,"value") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"value","hash":{},"data":data,"loc":{"start":{"line":18,"column":34},"end":{"line":18,"column":43}}}) : helper)))
    + "</option>\n      ";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":17},"end":{"line":1,"column":25}}}) : helper)))
    + "</td>\n<td>\n  <select "
    + ((stack1 = (lookupProperty(helpers,"isArray")||(depth0 && lookupProperty(depth0,"isArray"))||alias2).call(alias1,depth0,{"name":"isArray","hash":{},"fn":container.program(0, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":3,"column":10},"end":{"line":3,"column":59}}})) != null ? stack1 : "")
    + " class='parameter' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":3,"column":84},"end":{"line":3,"column":92}}}) : helper)))
    + "'>\n    "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"required") : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(2, data, 0),"data":data,"loc":{"start":{"line":4,"column":4},"end":{"line":13,"column":11}}})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = lookupProperty(helpers,"each").call(alias1,((stack1 = (depth0 != null ? lookupProperty(depth0,"allowableValues") : depth0)) != null ? lookupProperty(stack1,"descriptiveValues") : stack1),{"name":"each","hash":{},"fn":container.program(7, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":14,"column":4},"end":{"line":20,"column":13}}})) != null ? stack1 : "")
    + "\n  </select>\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":23,"column":4},"end":{"line":23,"column":21}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"paramType") || (depth0 != null ? lookupProperty(depth0,"paramType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"paramType","hash":{},"data":data,"loc":{"start":{"line":24,"column":4},"end":{"line":24,"column":19}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td><span class=\"model-signature\"></span></td>";
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
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        "
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(2, data, 0),"inverse":container.program(3, data, 0),"data":data,"loc":{"start":{"line":6,"column":8},"end":{"line":10,"column":15}}})) != null ? stack1 : "")
    + "\n    ";
},"2":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n            "
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":7,"column":12},"end":{"line":7,"column":28}}}) : helper)))
    + "\n        ";
},"3":function(container,depth0,helpers,partials,data) {
    return "\n            (empty)\n        ";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code'>"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":17},"end":{"line":1,"column":25}}}) : helper)))
    + "</td>\n<td>\n    "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isBody") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(1, data, 0),"data":data,"loc":{"start":{"line":3,"column":4},"end":{"line":11,"column":11}}})) != null ? stack1 : "")
    + "\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":13,"column":4},"end":{"line":13,"column":21}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"paramType") || (depth0 != null ? lookupProperty(depth0,"paramType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"paramType","hash":{},"data":data,"loc":{"start":{"line":14,"column":4},"end":{"line":14,"column":19}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td><span class=\"model-signature\"></span></td>\n";
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
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n        "
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(2, data, 0),"inverse":container.program(3, data, 0),"data":data,"loc":{"start":{"line":6,"column":8},"end":{"line":10,"column":15}}})) != null ? stack1 : "")
    + "\n    ";
},"2":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n            "
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":7,"column":12},"end":{"line":7,"column":28}}}) : helper)))
    + "\n        ";
},"3":function(container,depth0,helpers,partials,data) {
    return "\n            (empty)\n        ";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code required'>"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":26},"end":{"line":1,"column":34}}}) : helper)))
    + "</td>\n<td>\n    "
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isBody") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(1, data, 0),"data":data,"loc":{"start":{"line":3,"column":4},"end":{"line":11,"column":11}}})) != null ? stack1 : "")
    + "\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":13,"column":4},"end":{"line":13,"column":21}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"paramType") || (depth0 != null ? lookupProperty(depth0,"paramType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"paramType","hash":{},"data":data,"loc":{"start":{"line":14,"column":4},"end":{"line":14,"column":19}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td><span class=\"model-signature\"></span></td>\n";
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
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"isFile") : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(2, data, 0),"data":data,"loc":{"start":{"line":4,"column":2},"end":{"line":14,"column":9}}})) != null ? stack1 : "")
    + "\n\t";
},"1":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t<input type=\"file\" name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":5,"column":28},"end":{"line":5,"column":36}}}) : helper)))
    + "'/>\n\t\t";
},"2":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(3, data, 0),"inverse":container.program(4, data, 0),"data":data,"loc":{"start":{"line":7,"column":3},"end":{"line":13,"column":10}}})) != null ? stack1 : "")
    + "\n\t\t";
},"3":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t\t<textarea class='body-textarea' placeholder='(required)' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":8,"column":67},"end":{"line":8,"column":75}}}) : helper)))
    + "'>"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":8,"column":77},"end":{"line":8,"column":93}}}) : helper)))
    + "</textarea>\n\t\t\t";
},"4":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t\t<textarea class='body-textarea' placeholder='(required)' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":10,"column":67},"end":{"line":10,"column":75}}}) : helper)))
    + "'></textarea>\n\t\t\t\t<br />\n\t\t\t\t<div class=\"parameter-content-type\" />\n\t\t\t";
},"5":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"isFile") : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.program(7, data, 0),"data":data,"loc":{"start":{"line":16,"column":2},"end":{"line":24,"column":9}}})) != null ? stack1 : "")
    + "\n\t";
},"6":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t<input class='parameter' class='required' type='file' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":17,"column":63},"end":{"line":17,"column":71}}}) : helper)))
    + "'/>\n\t\t";
},"7":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0),{"name":"if","hash":{},"fn":container.program(8, data, 0),"inverse":container.program(9, data, 0),"data":data,"loc":{"start":{"line":19,"column":3},"end":{"line":23,"column":10}}})) != null ? stack1 : "")
    + "\n\t\t";
},"8":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t\t<input class='parameter required' minlength='1' name='"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":20,"column":58},"end":{"line":20,"column":66}}}) : helper)))
    + "' placeholder='(required)' type='text' value='"
    + alias4(((helper = (helper = lookupProperty(helpers,"defaultValue") || (depth0 != null ? lookupProperty(depth0,"defaultValue") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"defaultValue","hash":{},"data":data,"loc":{"start":{"line":20,"column":112},"end":{"line":20,"column":128}}}) : helper)))
    + "'/>\n\t\t\t";
},"9":function(container,depth0,helpers,partials,data) {
    var helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n\t\t\t\t<input class='parameter required' minlength='1' name='"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"name","hash":{},"data":data,"loc":{"start":{"line":22,"column":58},"end":{"line":22,"column":66}}}) : helper)))
    + "' placeholder='(required)' type='text' value=''/>\n\t\t\t";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td class='code required'>"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":1,"column":26},"end":{"line":1,"column":34}}}) : helper)))
    + "</td>\n<td>\n\t"
    + ((stack1 = lookupProperty(helpers,"if").call(alias1,(depth0 != null ? lookupProperty(depth0,"isBody") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(5, data, 0),"data":data,"loc":{"start":{"line":3,"column":1},"end":{"line":25,"column":8}}})) != null ? stack1 : "")
    + "\n</td>\n<td>\n\t<strong>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data,"loc":{"start":{"line":28,"column":9},"end":{"line":28,"column":26}}}) : helper))) != null ? stack1 : "")
    + "</strong>\n</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"paramType") || (depth0 != null ? lookupProperty(depth0,"paramType") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"paramType","hash":{},"data":data,"loc":{"start":{"line":30,"column":4},"end":{"line":30,"column":19}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td><span class=\"model-signature\"></span></td>\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['parameter_content_type'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n  "
    + ((stack1 = lookupProperty(helpers,"each").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"consumes") : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":4,"column":2},"end":{"line":6,"column":11}}})) != null ? stack1 : "")
    + "\n";
},"1":function(container,depth0,helpers,partials,data) {
    var stack1, alias1=container.lambda;

  return "\n  <option value=\""
    + ((stack1 = alias1(depth0, depth0)) != null ? stack1 : "")
    + "\">"
    + ((stack1 = alias1(depth0, depth0)) != null ? stack1 : "")
    + "</option>\n  ";
},"2":function(container,depth0,helpers,partials,data) {
    return "\n  <option value=\"application/json\">application/json</option>\n";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<label for=\"parameterContentType\"></label>\n<select name=\"parameterContentType\">\n"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"consumes") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(2, data, 0),"data":data,"loc":{"start":{"line":3,"column":0},"end":{"line":9,"column":7}}})) != null ? stack1 : "")
    + "\n</select>\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['resource'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var stack1, helper, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return " : "
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : container.hooks.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}),{"name":"description","hash":{},"data":data,"loc":{"start":{"line":3,"column":112},"end":{"line":3,"column":129}}}) : helper))) != null ? stack1 : "");
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, options, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", alias4=container.escapeExpression, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    }, buffer =
  "<div class='heading'>\n  <h2>\n    <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"id") || (depth0 != null ? lookupProperty(depth0,"id") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data,"loc":{"start":{"line":3,"column":16},"end":{"line":3,"column":22}}}) : helper)))
    + "' onclick=\"Docs.toggleEndpointListForResource('"
    + alias4(((helper = (helper = lookupProperty(helpers,"id") || (depth0 != null ? lookupProperty(depth0,"id") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data,"loc":{"start":{"line":3,"column":69},"end":{"line":3,"column":75}}}) : helper)))
    + "');\">"
    + alias4(((helper = (helper = lookupProperty(helpers,"name") || (depth0 != null ? lookupProperty(depth0,"name") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data,"loc":{"start":{"line":3,"column":80},"end":{"line":3,"column":88}}}) : helper)))
    + "</a> ";
  stack1 = ((helper = (helper = lookupProperty(helpers,"description") || (depth0 != null ? lookupProperty(depth0,"description") : depth0)) != null ? helper : alias2),(options={"name":"description","hash":{},"fn":container.program(0, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":3,"column":93},"end":{"line":3,"column":145}}}),(typeof helper === alias3 ? helper.call(alias1,options) : helper));
  if (!lookupProperty(helpers,"description")) { stack1 = container.hooks.blockHelperMissing.call(depth0,stack1,options)}
  if (stack1 != null) { buffer += stack1; }
  return buffer + "\n  </h2>\n  <ul class='options'>\n    <li>\n      <a href='#!/"
    + alias4(((helper = (helper = lookupProperty(helpers,"id") || (depth0 != null ? lookupProperty(depth0,"id") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data,"loc":{"start":{"line":7,"column":18},"end":{"line":7,"column":24}}}) : helper)))
    + "' id='endpointListTogger_"
    + alias4(((helper = (helper = lookupProperty(helpers,"id") || (depth0 != null ? lookupProperty(depth0,"id") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data,"loc":{"start":{"line":7,"column":49},"end":{"line":7,"column":55}}}) : helper)))
    + "'\n         onclick=\"Docs.toggleEndpointListForResource('"
    + alias4(((helper = (helper = lookupProperty(helpers,"id") || (depth0 != null ? lookupProperty(depth0,"id") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data,"loc":{"start":{"line":8,"column":54},"end":{"line":8,"column":60}}}) : helper)))
    + "');\">Show/Hide</a>\n    </li>\n    <li>\n      <a href='#' onclick=\"Docs.collapseOperationsForResource('"
    + alias4(((helper = (helper = lookupProperty(helpers,"id") || (depth0 != null ? lookupProperty(depth0,"id") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data,"loc":{"start":{"line":11,"column":63},"end":{"line":11,"column":69}}}) : helper)))
    + "'); return false;\">\n        List Operations\n      </a>\n    </li>\n    <li>\n      <a href='#' onclick=\"Docs.expandOperationsForResource('"
    + alias4(((helper = (helper = lookupProperty(helpers,"id") || (depth0 != null ? lookupProperty(depth0,"id") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data,"loc":{"start":{"line":16,"column":61},"end":{"line":16,"column":67}}}) : helper)))
    + "'); return false;\">\n        Expand Operations\n      </a>\n    </li>\n    <li>\n      <a href='"
    + alias4(((helper = (helper = lookupProperty(helpers,"url") || (depth0 != null ? lookupProperty(depth0,"url") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"url","hash":{},"data":data,"loc":{"start":{"line":21,"column":15},"end":{"line":21,"column":22}}}) : helper)))
    + "'>Raw</a>\n    </li>\n  </ul>\n</div>\n<ul class='endpoints' id='"
    + alias4(((helper = (helper = lookupProperty(helpers,"id") || (depth0 != null ? lookupProperty(depth0,"id") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data,"loc":{"start":{"line":25,"column":26},"end":{"line":25,"column":32}}}) : helper)))
    + "_endpoint_list' style='display:none'>\n\n</ul>\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['response_content_type'] = Handlebars.template({"0":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "\n  "
    + ((stack1 = lookupProperty(helpers,"each").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"produces") : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data,"loc":{"start":{"line":4,"column":2},"end":{"line":6,"column":11}}})) != null ? stack1 : "")
    + "\n";
},"1":function(container,depth0,helpers,partials,data) {
    var stack1, alias1=container.lambda;

  return "\n  <option value=\""
    + ((stack1 = alias1(depth0, depth0)) != null ? stack1 : "")
    + "\">"
    + ((stack1 = alias1(depth0, depth0)) != null ? stack1 : "")
    + "</option>\n  ";
},"2":function(container,depth0,helpers,partials,data) {
    return "\n  <option value=\"application/json\">application/json</option>\n";
},"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<label for=\"responseContentType\"></label>\n<select name=\"responseContentType\">\n"
    + ((stack1 = lookupProperty(helpers,"if").call(depth0 != null ? depth0 : (container.nullContext || {}),(depth0 != null ? lookupProperty(depth0,"produces") : depth0),{"name":"if","hash":{},"fn":container.program(0, data, 0),"inverse":container.program(2, data, 0),"data":data,"loc":{"start":{"line":3,"column":0},"end":{"line":9,"column":7}}})) != null ? stack1 : "")
    + "\n</select>\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['signature'] = Handlebars.template({"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<div>\n<ul class=\"signature-nav\">\n    <li><a class=\"description-link\" href=\"#\">Model</a></li>\n    <li><a class=\"snippet-link\" href=\"#\">Model Schema</a></li>\n</ul>\n<div>\n\n<div class=\"signature-container\">\n    <div class=\"description\">\n        "
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"signature") || (depth0 != null ? lookupProperty(depth0,"signature") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"signature","hash":{},"data":data,"loc":{"start":{"line":10,"column":8},"end":{"line":10,"column":23}}}) : helper))) != null ? stack1 : "")
    + "\n    </div>\n\n    <div class=\"snippet\">\n        <pre><code>"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"sampleJSON") || (depth0 != null ? lookupProperty(depth0,"sampleJSON") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"sampleJSON","hash":{},"data":data,"loc":{"start":{"line":14,"column":19},"end":{"line":14,"column":33}}}) : helper)))
    + "</code></pre>\n        <small class=\"notice\"></small>\n    </div>\n</div>\n\n";
},"useData":true});
})();

(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['status_code'] = Handlebars.template({"compiler":[8,">= 4.3.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=container.hooks.helperMissing, alias3="function", lookupProperty = container.lookupProperty || function(parent, propertyName) {
        if (Object.prototype.hasOwnProperty.call(parent, propertyName)) {
          return parent[propertyName];
        }
        return undefined
    };

  return "<td width='15%' class='code'>"
    + container.escapeExpression(((helper = (helper = lookupProperty(helpers,"code") || (depth0 != null ? lookupProperty(depth0,"code") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"code","hash":{},"data":data,"loc":{"start":{"line":1,"column":29},"end":{"line":1,"column":37}}}) : helper)))
    + "</td>\n<td>"
    + ((stack1 = ((helper = (helper = lookupProperty(helpers,"message") || (depth0 != null ? lookupProperty(depth0,"message") : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"message","hash":{},"data":data,"loc":{"start":{"line":2,"column":4},"end":{"line":2,"column":17}}}) : helper))) != null ? stack1 : "")
    + "</td>\n<td width='50%'><span class=\"model-signature\" /></td>";
},"useData":true});
})();



// Generated by CoffeeScript 1.5.0
(function() {
  var ContentTypeView, HeaderView, MainView, OperationView, ParameterContentTypeView, ParameterView, ResourceView, ResponseContentTypeView, SignatureView, StatusCodeView, SwaggerUi,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  SwaggerUi = (function(_super) {

    __extends(SwaggerUi, _super);

    function SwaggerUi() {
      SwaggerUi.__super__.constructor.apply(this, arguments);
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
      if ($('#' + this.dom_id) == null) {
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
      this.options.url = data.url;
      return this.load();
    };

    SwaggerUi.prototype.load = function() {
      var url, _ref;
      if ((_ref = this.mainView) != null) {
        _ref.clear();
      }
      url = this.options.url;
      if (url.indexOf("http") !== 0) {
        url = this.buildUrl(window.location.href.toString(), url);
      }
      this.options.url = url;
      this.headerView.update(url);
      this.api = new SwaggerApi(this.options);
      this.api.build();
      return this.api;
    };

    SwaggerUi.prototype.render = function() {
      var _this = this;
      this.showMessage('Finished Loading Resource Information. Rendering Swagger UI...');
      this.headerView.updateInfo(this.api.info);
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

    SwaggerUi.prototype.buildUrl = function(base, url) {
      var endOfPath, parts;
      log("base is " + base);
      if (url.indexOf("/") === 0) {
        parts = base.split("/");
        base = parts[0] + "//" + parts[2];
        return base + url;
      } else {
        endOfPath = base.length;
        if (base.indexOf("?") > -1) {
          endOfPath = Math.min(endOfPath, base.indexOf("?"));
        }
        if (base.indexOf("#") > -1) {
          endOfPath = Math.min(endOfPath, base.indexOf("#"));
        }
        base = base.substring(0, endOfPath);
        if (base.indexOf("/", base.length - 1) !== -1) {
          return base + url;
        }
        return base + "/" + url;
      }
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
      HeaderView.__super__.constructor.apply(this, arguments);
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
        url: "http://petstore.swagger.wordnik.com/api/api-docs"
      });
    };

    HeaderView.prototype.showWordnikDev = function(e) {
      return this.trigger('update-swagger-ui', {
        url: "http://api.wordnik.com/v4/resources.json"
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
        url: $('#input_baseUrl').val(),
        apiKey: $('#input_apiKey').val()
      });
    };

    HeaderView.prototype.update = function(url, apiKey, trigger) {
      if (trigger == null) {
        trigger = false;
      }
      $('#input_baseUrl').val(url);
      if (trigger) {
        return this.trigger('update-swagger-ui', {
          url: url
        });
      }
    };

    HeaderView.prototype.updateInfo = function(info) {
      if (info.title != null) {
        $('#logo').text(info.title);
      }
      if (info.description != null) {
        $('#logo').attr('title', info.description);
      }
      if (info.termsOfServiceUrl != null) {
        return $('#logo').attr('href', info.termsOfServiceUrl);
      }
    };

    return HeaderView;

  })(Backbone.View);

  MainView = (function(_super) {

    __extends(MainView, _super);

    function MainView() {
      MainView.__super__.constructor.apply(this, arguments);
    }

    MainView.prototype.initialize = function() {};

    MainView.prototype.render = function() {
      var counter, id, resource, resources, _i, _len, _ref;
      $(this.el).html(Handlebars.templates.main(this.model));
      resources = {};
      counter = 0;
      _ref = this.model.apisArray;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        resource = _ref[_i];
        id = resource.name;
        while (typeof resources[id] !== 'undefined') {
          id = id + "_" + counter;
          counter += 1;
        }
        resource.id = id;
        resources[id] = resource;
        this.addResource(resource);
      }
      return this;
    };

    MainView.prototype.addResource = function(resource) {
      var resourceView;
      resourceView = new ResourceView({
        model: resource,
        tagName: 'li',
        id: 'resource_' + resource.id,
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
      ResourceView.__super__.constructor.apply(this, arguments);
    }

    ResourceView.prototype.initialize = function() {};

    ResourceView.prototype.render = function() {
      var counter, id, methods, operation, _i, _len, _ref;
      $(this.el).html(Handlebars.templates.resource(this.model));
      methods = {};
      _ref = this.model.operationsArray;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        operation = _ref[_i];
        counter = 0;
        id = operation.nickname;
        while (typeof methods[id] !== 'undefined') {
          id = id + "_" + counter;
          counter += 1;
        }
        methods[id] = operation;
        operation.nickname = id;
        operation.parentId = this.model.id;
        this.addOperation(operation);
      }
      return this;
    };

    ResourceView.prototype.addOperation = function(operation) {
      var operationView;
      operation.number = this.number;
      operationView = new OperationView({
        model: operation,
        tagName: 'li',
        className: 'endpoint'
      });
      $('.endpoints', $(this.el)).append(operationView.render().el);
      return this.number++;
    };

    return ResourceView;

  })(Backbone.View);

  OperationView = (function(_super) {

    __extends(OperationView, _super);

    function OperationView() {
      OperationView.__super__.constructor.apply(this, arguments);
    }

    OperationView.prototype.invocationUrl = null;

    OperationView.prototype.events = {
      'submit .sandbox': 'submitOperation',
      'click .submit': 'submitOperation',
      'click .response_hider': 'hideResponse',
      'click .toggleOperation': 'toggleOperationContent',
      'mouseenter .api-ic': 'mouseEnter',
      'mouseout .api-ic': 'mouseExit'
    };

    OperationView.prototype.initialize = function() {};

    OperationView.prototype.mouseEnter = function(e) {
      var elem, hgh, pos, scMaxX, scMaxY, scX, scY, wd, x, y;
      elem = $(e.currentTarget.parentNode).find('#api_information_panel');
      x = event.pageX;
      y = event.pageY;
      scX = $(window).scrollLeft();
      scY = $(window).scrollTop();
      scMaxX = scX + $(window).width();
      scMaxY = scY + $(window).height();
      wd = elem.width();
      hgh = elem.height();
      if (x + wd > scMaxX) {
        x = scMaxX - wd;
      }
      if (x < scX) {
        x = scX;
      }
      if (y + hgh > scMaxY) {
        y = scMaxY - hgh;
      }
      if (y < scY) {
        y = scY;
      }
      pos = {};
      pos.top = y;
      pos.left = x;
      elem.css(pos);
      return $(e.currentTarget.parentNode).find('#api_information_panel').show();
    };

    OperationView.prototype.mouseExit = function(e) {
      return $(e.currentTarget.parentNode).find('#api_information_panel').hide();
    };

    OperationView.prototype.render = function() {
      var contentTypeModel, isMethodSubmissionSupported, k, o, param, responseContentTypeView, responseSignatureView, signatureModel, statusCode, type, v, _i, _j, _k, _l, _len, _len1, _len2, _len3, _ref, _ref1, _ref2, _ref3;
      isMethodSubmissionSupported = true;
      if (!isMethodSubmissionSupported) {
        this.model.isReadOnly = true;
      }
      this.model.oauth = null;
      if (this.model.authorizations) {
        _ref = this.model.authorizations;
        for (k in _ref) {
          v = _ref[k];
          if (k === "oauth2") {
            if (this.model.oauth === null) {
              this.model.oauth = {};
            }
            if (this.model.oauth.scopes === void 0) {
              this.model.oauth.scopes = [];
            }
            for (_i = 0, _len = v.length; _i < _len; _i++) {
              o = v[_i];
              this.model.oauth.scopes.push(o);
            }
          }
        }
      }
      $(this.el).html(Handlebars.templates.operation(this.model));
      if (this.model.responseClassSignature && this.model.responseClassSignature !== 'string') {
        signatureModel = {
          sampleJSON: this.model.responseSampleJSON,
          isParam: false,
          signature: this.model.responseClassSignature
        };
        responseSignatureView = new SignatureView({
          model: signatureModel,
          tagName: 'div'
        });
        $('.model-signature', $(this.el)).append(responseSignatureView.render().el);
      } else {
        $('.model-signature', $(this.el)).html(this.model.type);
      }
      contentTypeModel = {
        isParam: false
      };
      contentTypeModel.consumes = this.model.consumes;
      contentTypeModel.produces = this.model.produces;
      _ref1 = this.model.parameters;
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        param = _ref1[_j];
        type = param.type || param.dataType;
        if (type.toLowerCase() === 'file') {
          if (!contentTypeModel.consumes) {
            log("set content type ");
            contentTypeModel.consumes = 'multipart/form-data';
          }
        }
      }
      responseContentTypeView = new ResponseContentTypeView({
        model: contentTypeModel
      });
      $('.response-content-type', $(this.el)).append(responseContentTypeView.render().el);
      _ref2 = this.model.parameters;
      for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
        param = _ref2[_k];
        this.addParameter(param, contentTypeModel.consumes);
      }
      _ref3 = this.model.responseMessages;
      for (_l = 0, _len3 = _ref3.length; _l < _len3; _l++) {
        statusCode = _ref3[_l];
        this.addStatusCode(statusCode);
      }
      return this;
    };

    OperationView.prototype.addParameter = function(param, consumes) {
      var paramView;
      param.consumes = consumes;
      paramView = new ParameterView({
        model: param,
        tagName: 'tr',
        readOnly: this.model.isReadOnly
      });
      return $('.operation-params', $(this.el)).append(paramView.render().el);
    };

    OperationView.prototype.addStatusCode = function(statusCode) {
      var statusCodeView;
      statusCodeView = new StatusCodeView({
        model: statusCode,
        tagName: 'tr'
      });
      return $('.operation-status', $(this.el)).append(statusCodeView.render().el);
    };

    OperationView.prototype.submitOperation = function(e) {
      var error_free, form, isFileUpload, map, o, opts, val, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2;
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
        opts = {
          parent: this
        };
        isFileUpload = false;
        _ref = form.find("input");
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          o = _ref[_i];
          if ((o.value != null) && jQuery.trim(o.value).length > 0) {
            map[o.name] = o.value;
          }
          if (o.type === "file") {
            isFileUpload = true;
          }
        }
        _ref1 = form.find("textarea");
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          o = _ref1[_j];
          if ((o.value != null) && jQuery.trim(o.value).length > 0) {
            map["body"] = o.value;
          }
        }
        _ref2 = form.find("select");
        for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
          o = _ref2[_k];
          val = this.getSelectedValue(o);
          if ((val != null) && jQuery.trim(val).length > 0) {
            map[o.name] = val;
          }
        }
        opts.responseContentType = $("div select[name=responseContentType]", $(this.el)).val();
        opts.requestContentType = $("div select[name=parameterContentType]", $(this.el)).val();
        $(".response_throbber", $(this.el)).show();
        if (isFileUpload) {
          return this.handleFileUpload(map, form);
        } else {
          return this.model["do"](map, opts, this.showCompleteStatus, this.showErrorStatus, this);
        }
      }
    };

    OperationView.prototype.success = function(response, parent) {
      return parent.showCompleteStatus(response);
    };

    OperationView.prototype.handleFileUpload = function(map, form) {
      var bodyParam, el, headerParams, o, obj, param, params, _i, _j, _k, _l, _len, _len1, _len2, _len3, _ref, _ref1, _ref2, _ref3,
        _this = this;
      _ref = form.serializeArray();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        o = _ref[_i];
        if ((o.value != null) && jQuery.trim(o.value).length > 0) {
          map[o.name] = o.value;
        }
      }
      bodyParam = new FormData();
      params = 0;
      _ref1 = this.model.parameters;
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        param = _ref1[_j];
        if (param.paramType === 'form') {
          if (map[param.name] !== void 0) {
            bodyParam.append(param.name, map[param.name]);
          }
        }
      }
      headerParams = {};
      _ref2 = this.model.parameters;
      for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
        param = _ref2[_k];
        if (param.paramType === 'header') {
          headerParams[param.name] = map[param.name];
        }
      }
      log(headerParams);
      _ref3 = form.find('input[type~="file"]');
      for (_l = 0, _len3 = _ref3.length; _l < _len3; _l++) {
        el = _ref3[_l];
        if (typeof el.files[0] !== 'undefined') {
          bodyParam.append($(el).attr('name'), el.files[0]);
          params += 1;
        }
      }
      log(bodyParam);
      this.invocationUrl = this.model.supportHeaderParams() ? (headerParams = this.model.getHeaderParams(map), this.model.urlify(map, false)) : this.model.urlify(map, true);
      $(".request_url", $(this.el)).html("<pre>" + this.invocationUrl + "</pre>");
      obj = {
        type: this.model.method,
        url: this.invocationUrl,
        headers: headerParams,
        data: bodyParam,
        dataType: 'json',
        contentType: false,
        processData: false,
        error: function(data, textStatus, error) {
          return _this.showErrorStatus(_this.wrap(data), _this);
        },
        success: function(data) {
          return _this.showResponse(data, _this);
        },
        complete: function(data) {
          return _this.showCompleteStatus(_this.wrap(data), _this);
        }
      };
      if (window.authorizations) {
        window.authorizations.apply(obj);
      }
      if (params === 0) {
        obj.data.append("fake", "true");
      }
      jQuery.ajax(obj);
      return false;
    };

    OperationView.prototype.wrap = function(data) {
      var h, headerArray, headers, i, o, _i, _len;
      headers = {};
      headerArray = data.getAllResponseHeaders().split("\r");
      for (_i = 0, _len = headerArray.length; _i < _len; _i++) {
        i = headerArray[_i];
        h = i.split(':');
        if (h[0] !== void 0 && h[1] !== void 0) {
          headers[h[0].trim()] = h[1].trim();
        }
      }
      o = {};
      o.content = {};
      o.content.data = data.responseText;
      o.headers = headers;
      o.request = {};
      o.request.url = this.invocationUrl;
      o.status = data.status;
      return o;
    };

    OperationView.prototype.getSelectedValue = function(select) {
      var opt, options, _i, _len, _ref;
      if (!select.multiple) {
        return select.value;
      } else {
        options = [];
        _ref = select.options;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          opt = _ref[_i];
          if (opt.selected) {
            options.push(opt.value);
          }
        }
        if (options.length > 0) {
          return options.join(",");
        } else {
          return null;
        }
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
      return $(".response_body", $(this.el)).html(escape(prettyJson));
    };

    OperationView.prototype.showErrorStatus = function(data, parent) {
      return parent.showStatus(data);
    };

    OperationView.prototype.showCompleteStatus = function(data, parent) {
      return parent.showStatus(data);
    };

    OperationView.prototype.formatXml = function(xml) {
      var contexp, formatted, indent, lastType, lines, ln, pad, reg, transitions, wsexp, _fn, _i, _len;
      reg = /(>)(<)(\/*)/g;
      wsexp = /[ ]*(.*)[ ]+\n/g;
      contexp = /(<.+>)(.+\n)/g;
      xml = xml.replace(reg, '$1\n$2$3').replace(wsexp, '$1\n').replace(contexp, '$1\n$2');
      pad = 0;
      formatted = '';
      lines = xml.split('\n');
      indent = 0;
      lastType = 'other';
      transitions = {
        'single->single': 0,
        'single->closing': -1,
        'single->opening': 0,
        'single->other': 0,
        'closing->single': 0,
        'closing->closing': -1,
        'closing->opening': 0,
        'closing->other': 0,
        'opening->single': 1,
        'opening->closing': 0,
        'opening->opening': 1,
        'opening->other': 1,
        'other->single': 0,
        'other->closing': -1,
        'other->opening': 0,
        'other->other': 0
      };
      _fn = function(ln) {
        var fromTo, j, key, padding, type, types, value;
        types = {
          single: Boolean(ln.match(/<.+\/>/)),
          closing: Boolean(ln.match(/<\/.+>/)),
          opening: Boolean(ln.match(/<[^!?].*>/))
        };
        type = ((function() {
          var _results;
          _results = [];
          for (key in types) {
            value = types[key];
            if (value) {
              _results.push(key);
            }
          }
          return _results;
        })())[0];
        type = type === void 0 ? 'other' : type;
        fromTo = lastType + '->' + type;
        lastType = type;
        padding = '';
        indent += transitions[fromTo];
        padding = ((function() {
          var _j, _ref, _results;
          _results = [];
          for (j = _j = 0, _ref = indent; 0 <= _ref ? _j < _ref : _j > _ref; j = 0 <= _ref ? ++_j : --_j) {
            _results.push('  ');
          }
          return _results;
        })()).join('');
        if (fromTo === 'opening->closing') {
          return formatted = formatted.substr(0, formatted.length - 1) + ln + '\n';
        } else {
          return formatted += padding + ln + '\n';
        }
      };
      for (_i = 0, _len = lines.length; _i < _len; _i++) {
        ln = lines[_i];
        _fn(ln);
      }
      return formatted;
    };

    OperationView.prototype.showStatus = function(response) {
      var code, content, contentType, headers, pre, response_body, url;
      if (response.content === void 0) {
        content = response.data;
        url = response.url;
      } else {
        content = response.content.data;
        url = response.request.url;
      }
      headers = response.headers;
      contentType = headers && headers["Content-Type"] ? headers["Content-Type"].split(";")[0].trim() : null;
      if (!content) {
        code = $('<code />').text("no content");
        pre = $('<pre class="json" />').append(code);
      } else if (contentType === "application/json" || /\+json$/.test(contentType)) {
        code = $('<code />').text(JSON.stringify(JSON.parse(content), null, "  "));
        pre = $('<pre class="json" />').append(code);
      } else if (contentType === "application/xml" || /\+xml$/.test(contentType)) {
        code = $('<code />').text(this.formatXml(content));
        pre = $('<pre class="xml" />').append(code);
      } else if (contentType === "text/html") {
        code = $('<code />').html(content);
        pre = $('<pre class="xml" />').append(code);
      } else if (/^image\//.test(contentType)) {
        pre = $('<img>').attr('src', url);
      } else {
        code = $('<code />').text(content);
        pre = $('<pre class="json" />').append(code);
      }
      response_body = pre;
      $(".request_url", $(this.el)).html("<pre>" + url + "</pre>");
      $(".response_code", $(this.el)).html("<pre>" + response.status + "</pre>");
      $(".response_body", $(this.el)).html(response_body);
      $(".response_headers", $(this.el)).html("<pre>" + JSON.stringify(response.headers, null, "  ").replace(/\n/g, "<br>") + "</pre>");
      $(".response", $(this.el)).slideDown();
      $(".response_hider", $(this.el)).show();
      $(".response_throbber", $(this.el)).hide();
      return hljs.highlightBlock($('.response_body', $(this.el))[0]);
    };

    OperationView.prototype.toggleOperationContent = function() {
      var elem;
      elem = $('#' + Docs.escapeResourceName(this.model.parentId) + "_" + this.model.nickname + "_content");
      if (elem.is(':visible')) {
        return Docs.collapseOperation(elem);
      } else {
        return Docs.expandOperation(elem);
      }
    };

    return OperationView;

  })(Backbone.View);

  StatusCodeView = (function(_super) {

    __extends(StatusCodeView, _super);

    function StatusCodeView() {
      StatusCodeView.__super__.constructor.apply(this, arguments);
    }

    StatusCodeView.prototype.initialize = function() {};

    StatusCodeView.prototype.render = function() {
      var responseModel, responseModelView, template;
      template = this.template();
      $(this.el).html(template(this.model));
      if (swaggerUi.api.models.hasOwnProperty(this.model.responseModel)) {
        responseModel = {
          sampleJSON: JSON.stringify(swaggerUi.api.models[this.model.responseModel].createJSONSample(), null, 2),
          isParam: false,
          signature: swaggerUi.api.models[this.model.responseModel].getMockSignature()
        };
        responseModelView = new SignatureView({
          model: responseModel,
          tagName: 'div'
        });
        $('.model-signature', this.$el).append(responseModelView.render().el);
      } else {
        $('.model-signature', this.$el).html('');
      }
      return this;
    };

    StatusCodeView.prototype.template = function() {
      return Handlebars.templates.status_code;
    };

    return StatusCodeView;

  })(Backbone.View);

  ParameterView = (function(_super) {

    __extends(ParameterView, _super);

    function ParameterView() {
      ParameterView.__super__.constructor.apply(this, arguments);
    }

    ParameterView.prototype.initialize = function() {
      return Handlebars.registerHelper('isArray', function(param, opts) {
        if (param.type.toLowerCase() === 'array' || param.allowMultiple) {
          return opts.fn(this);
        } else {
          return opts.inverse(this);
        }
      });
    };

    ParameterView.prototype.render = function() {
      var contentTypeModel, isParam, parameterContentTypeView, responseContentTypeView, signatureModel, signatureView, template, type;
      type = this.model.type || this.model.dataType;
      if (this.model.paramType === 'body') {
        this.model.isBody = true;
      }
      if (type.toLowerCase() === 'file') {
        this.model.isFile = true;
      }
      template = this.template();
      $(this.el).html(template(this.model));
      signatureModel = {
        sampleJSON: this.model.sampleJSON,
        isParam: true,
        signature: this.model.signature
      };
      if (this.model.sampleJSON) {
        signatureView = new SignatureView({
          model: signatureModel,
          tagName: 'div'
        });
        $('.model-signature', $(this.el)).append(signatureView.render().el);
      } else {
        $('.model-signature', $(this.el)).html(this.model.signature);
      }
      isParam = false;
      if (this.model.isBody) {
        isParam = true;
      }
      contentTypeModel = {
        isParam: isParam
      };
      contentTypeModel.consumes = this.model.consumes;
      if (isParam) {
        parameterContentTypeView = new ParameterContentTypeView({
          model: contentTypeModel
        });
        $('.parameter-content-type', $(this.el)).append(parameterContentTypeView.render().el);
      } else {
        responseContentTypeView = new ResponseContentTypeView({
          model: contentTypeModel
        });
        $('.response-content-type', $(this.el)).append(responseContentTypeView.render().el);
      }
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

  SignatureView = (function(_super) {

    __extends(SignatureView, _super);

    function SignatureView() {
      SignatureView.__super__.constructor.apply(this, arguments);
    }

    SignatureView.prototype.events = {
      'click a.description-link': 'switchToDescription',
      'click a.snippet-link': 'switchToSnippet',
      'mousedown .snippet': 'snippetToTextArea'
    };

    SignatureView.prototype.initialize = function() {};

    SignatureView.prototype.render = function() {
      var template;
      template = this.template();
      $(this.el).html(template(this.model));
      this.switchToDescription();
      this.isParam = this.model.isParam;
      if (this.isParam) {
        $('.notice', $(this.el)).text('Click to set as parameter value');
      }
      return this;
    };

    SignatureView.prototype.template = function() {
      return Handlebars.templates.signature;
    };

    SignatureView.prototype.switchToDescription = function(e) {
      if (e != null) {
        e.preventDefault();
      }
      $(".snippet", $(this.el)).hide();
      $(".description", $(this.el)).show();
      $('.description-link', $(this.el)).addClass('selected');
      return $('.snippet-link', $(this.el)).removeClass('selected');
    };

    SignatureView.prototype.switchToSnippet = function(e) {
      if (e != null) {
        e.preventDefault();
      }
      $(".description", $(this.el)).hide();
      $(".snippet", $(this.el)).show();
      $('.snippet-link', $(this.el)).addClass('selected');
      return $('.description-link', $(this.el)).removeClass('selected');
    };

    SignatureView.prototype.snippetToTextArea = function(e) {
      var textArea;
      if (this.isParam) {
        if (e != null) {
          e.preventDefault();
        }
        textArea = $('textarea', $(this.el.parentNode.parentNode.parentNode));
        if ($.trim(textArea.val()) === '') {
          return textArea.val(this.model.sampleJSON);
        }
      }
    };

    return SignatureView;

  })(Backbone.View);

  ContentTypeView = (function(_super) {

    __extends(ContentTypeView, _super);

    function ContentTypeView() {
      ContentTypeView.__super__.constructor.apply(this, arguments);
    }

    ContentTypeView.prototype.initialize = function() {};

    ContentTypeView.prototype.render = function() {
      var template;
      template = this.template();
      $(this.el).html(template(this.model));
      $('label[for=contentType]', $(this.el)).text('Response Content Type');
      return this;
    };

    ContentTypeView.prototype.template = function() {
      return Handlebars.templates.content_type;
    };

    return ContentTypeView;

  })(Backbone.View);

  ResponseContentTypeView = (function(_super) {

    __extends(ResponseContentTypeView, _super);

    function ResponseContentTypeView() {
      ResponseContentTypeView.__super__.constructor.apply(this, arguments);
    }

    ResponseContentTypeView.prototype.initialize = function() {};

    ResponseContentTypeView.prototype.render = function() {
      var template;
      template = this.template();
      $(this.el).html(template(this.model));
      $('label[for=responseContentType]', $(this.el)).text('Response Content Type');
      return this;
    };

    ResponseContentTypeView.prototype.template = function() {
      return Handlebars.templates.response_content_type;
    };

    return ResponseContentTypeView;

  })(Backbone.View);

  ParameterContentTypeView = (function(_super) {

    __extends(ParameterContentTypeView, _super);

    function ParameterContentTypeView() {
      ParameterContentTypeView.__super__.constructor.apply(this, arguments);
    }

    ParameterContentTypeView.prototype.initialize = function() {};

    ParameterContentTypeView.prototype.render = function() {
      var template;
      template = this.template();
      $(this.el).html(template(this.model));
      $('label[for=parameterContentType]', $(this.el)).text('Parameter content type:');
      return this;
    };

    ParameterContentTypeView.prototype.template = function() {
      return Handlebars.templates.parameter_content_type;
    };

    return ParameterContentTypeView;

  })(Backbone.View);

}).call(this);
