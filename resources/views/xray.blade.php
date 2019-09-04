<div id="xray-bar" style="display:none">
    <div id="xray-bar-controller-path">
        <span class="xray-bar-btn xray-bar-controller xray-icon-flash">
            <b></b>
            {{ \Illuminate\Support\Arr::get($routeInformation, 'controller', '-')}}
        </span>
        <span class="xray-bar-btn xray-bar-layout xray-icon-columns">
            <b></b>
            {{ $viewPath }}
        </span>
        <span class="xray-bar-btn xray-bar-view xray-icon-doc">
            {{ $viewName }}
        </span>
    </div>
</div>