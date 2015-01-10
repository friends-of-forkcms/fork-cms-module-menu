{*
    variables that are available:
    - {$widgetMenus}:
*}


{option:widgetMenus}
    <div class="menus-holder">
        <div class="inner">
            {*<h3 class="fancy">{$lblMenus|ucfirst}</h3>*}

            <div class="menus" id="menu-overview">
                {option:widgetMenus}
                    <ul class="menu-tabs">
                        {iteration:widgetMenus}
                            <li {option:widgetMenus.first}class="active"{/option:widgetMenus.first}><a href="#{$widgetMenus.url}"><i class="icon-angle-right" aria-hidden="true"></i> {$widgetMenus.title}</a></li>
                        {/iteration:widgetMenus}
                    </ul>

                    <div class="tab-content">
                        {iteration:widgetMenus}
                            <div class="tab-pane fade {option:widgetMenus.first}in active{/option:widgetMenus.first}" id="{$widgetMenus.url}">
                                <div class="description">
                                    {$widgetMenus.description}
                                </div>
                                <div class="price">
                                    {option:widgetMenus.price}
                                        {$widgetMenus.price}
                                    {/option:widgetMenus.price}
                                </div>
                            </div>
                        {/iteration:widgetMenus}
                    </div>
                {/option:widgetMenus}
            </div>
        </div>
    </div>
{/option:widgetMenus}
