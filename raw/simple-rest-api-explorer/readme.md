# Simple REST API Explorer

A simple way to showcasing and exploring all endpoints of your RESTful API.
Built by Bramus! - [http://www.bram.us/](http://www.bram.us/)

## Configuration

Edit the tad of JavaScript at the bottom of `index.html`

* `baseApiUrl`: Base URL of your API. All endpoints are a subdomain/subpage of this URL. eg. `http://api.website.tld/`
* `apiDataType`: The data type you expect back. Set this to `json` or `jsonp`.
* `apiExtraHeaders`: Extra headers you want to send along with your request. Object Literal formatted. Provide an empty object if no headers need to be sent. eg. an API-Key or authentication token: `{'X-API-Key':'1234567890'}`
* `apiUrlSuffix`: Extra suffix to add to each URL called. eg. an API-Key or authentication token: `'&token=1234567890'`

Update the `<dl>` in the `#sidebar` to hold the available endpoints of your API.

* The value of the `href` attribute of the `a` element is an example call to the endpoint.
* Provide a request method via a `data-requestmethod` attribute on the `a` element. Defaults to `get`
* Provide extra data to be sent via a `data-extradata` attribute on the `a` element. Format it like you'd format a querystring.
* For informational purposes, it is best to mention the request method and endpoint structure in the adjacent `small` element.

## Notes

* Only tested with APIs returning JSON or JSONP.
* Custom headers don't work with JSONP. If you do need both JSONP *and* an API-Key: ask your API provider to enable [CORS](http://www.html5rocks.com/en/tutorials/cors/) so you can switch to JSON.

Simple REST API Explorer is powered by (a tweaked) [Kelp JSON View](http://kelp.phate.org/2011/11/kelp-json-view-json-syntax-highlighting.html) and [Skeleton](http://www.getskeleton.com/)
