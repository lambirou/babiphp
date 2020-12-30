function executeCode() {
    var $jqLite = jQuery;
    var button = $jqLite("._bp-toggle-btn");
    var debugbar = $jqLite("._bp-debug-bar");
    var container = $jqLite("._bp-debug-content");
    var opened = false;

    button.on('click', function(event) {

        if (opened == false) {
            debugbar.animate({
                "bottom": "200px",
                "duration": 400,
                "easing": "linear",
                'callback': function(opts) {}
            });
            container.animate({
                "height": "200px",
                "duration": 400,
                "easing": "linear",
                'callback': function(opts) {
                    container.html('Some text and markup');
                }
            });

            button.addClass('opened');
            opened = true;
        } else {
            debugbar.animate({
                "bottom": "0px",
                "duration": 400,
                "easing": "linear",
                'callback': function(opts) {}
            });
            container.animate({
                "height": "0px",
                "duration": 400,
                "easing": "linear",
                'callback': function(opts) {}
            });

            button.removeClass('opened');
            opened = false;
        }

    });
}

if (typeof jQuery == 'undefined') {
    var headTag = document.getElementsByTagName("head")[0];
    var jqTag = document.createElement('script');
    jqTag.type = 'text/javascript';
    jqTag.innerHTML = '!function(){"undefined"==typeof $&&(window.$={});var e=[];document.addEventListener("DOMContentLoaded",function(){e.forEach(function(e){e()})},!1),$=function(t){var n;return t instanceof Function?(e.push(t),document):t instanceof Object?new DOMNodeCollection([t]):(t instanceof HTMLElement?n=[t]:(matches=t.match(/^<(\w+)>$/),n=matches?[document.createElement(matches[1])]:Array.prototype.slice.call(document.querySelectorAll(t))),new DOMNodeCollection(n))},DOMNodeCollection=function(e){return this.elements=e,this},DOMNodeCollection.prototype.html=function(e){return void 0!==e?(this.elements.forEach(function(t){t.innerHTML=e}),this):this.elements[0].innerHTML},DOMNodeCollection.prototype.empty=function(){return this.html(""),this},DOMNodeCollection.prototype.appendToFirst=function(e){this.elements[0].appendChild(e.cloneNode(!0))},DOMNodeCollection.prototype.append=function(e){return e instanceof DOMNodeCollection?e.elements.forEach(function(e){this.elements[0].appendChild(e.cloneNode(!0))}.bind(this)):e instanceof HTMLElement?this.elements[0].appendChild(e.cloneNode(!0)):"string"==typeof e&&this.elements.forEach(function(t){t.innerHTML+=e}),this},DOMNodeCollection.prototype.attr=function(e,t){return void 0!==t?(this.elements.forEach(function(n){n.setAttribute(e,t)}),this):this.elements[0].getAttribute(e)},DOMNodeCollection.prototype.get=function(e){return new DOMNodeCollection([this.elements[e]])},DOMNodeCollection.prototype.addClass=function(e){return this.elements.forEach(function(t){t.classList.add(e)}),this},DOMNodeCollection.prototype.removeClass=function(e){return this.elements.forEach(function(t){t.classList.remove(e)}),this},DOMNodeCollection.prototype.children=function(){var e,t=[];return this.elements.forEach(function(n){e=Array.prototype.slice.call(n.children),t=t.concat(e)}),new DOMNodeCollection(t)},DOMNodeCollection.prototype.parent=function(){var e,t=[];return this.elements.forEach(function(n){e=n.parentElement,-1===t.indexOf(e)&&t.push(e)}),new DOMNodeCollection(t)},DOMNodeCollection.prototype.find=function(e){var t,n,o=[];return this.elements.forEach(function(i){t=i.querySelectorAll(e),(n=Array.prototype.slice.call(t)).forEach(function(e){-1===o.indexOf(e)&&o.push(e)})}),new DOMNodeCollection(o)},DOMNodeCollection.prototype.remove=function(){this.elements.forEach(function(e){e.remove()})},DOMNodeCollection.prototype.on=function(e,t){return this.elements.forEach(function(n){n.addEventListener(e,t)}),this},DOMNodeCollection.prototype.off=function(e,t){return this.elements.forEach(function(n){n.removeEventListener(e,t)}),this},DOMNodeCollection.prototype.each=function(e){this.elements.forEach(e)},DOMNodeCollection.prototype.hide=function(){return this.originalDisplay=this.css("display"),this.css("display","none"),this},DOMNodeCollection.prototype.show=function(){return newDisplay=this.originalDisplay||"block",this.css("display",newDisplay),this},DOMNodeCollection.prototype.css=function(e,t){return void 0===t?this.elements[0].style.getPropertyValue(e):(this.elements.forEach(function(n){n.style[e]=t}),this)},DOMNodeCollection.prototype.text=function(e){return void 0!==e?(this.elements.forEach(function(t){t.innerText=e}),this):(text="",this.elements.forEach(function(e){text+=e.innerText}),text)},$.extend=function(e){Array.prototype.slice.call(arguments,1).forEach(function(t){for(var n in t)e[n]=t[n]})};var t=function(e){var t=new XMLHttpRequest;t.onreadystatechange=function(){t.readyState==XMLHttpRequest.DONE&&(200==t.status?e.success(t.response):e.error())},t.open(e.method,e.url,!0),"POST"==e.method&&t.setRequestHeader("Content-type",e.contentType),t.send(e.data)};$.ajax=function(e){var n={success:function(){},error:function(){},url:window.location.href,method:"GET",data:"",contentType:"application/x-www-form-urlencoded"};$.extend(n,e),t(n)},$.get=function(e,t){$.ajax({url:e,success:t})},$.post=function(e,t,n){$.ajax({url:e,data:t,success:n})}}();';
    jqTag.onload = executeCode;
    headTag.appendChild(jqTag);
} else {
    executeCode();
}