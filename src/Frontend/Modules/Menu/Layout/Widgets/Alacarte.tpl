{*
    variables that are available:
    - {$widgetMenuAlacarte}:
*}


{*{$widgetMenuAlacarte|dump}*}

{option:widgetMenuAlacarte}
    <div class="alacarte-holder">
        {*<h3 class="fancy">{$lblAlacarte|ucfirst}</h3>*}

        {iteration:widgetMenuAlacarte}
            <div class="alacarte menu-col-6">
                <h4>{$widgetMenuAlacarte.title}</h4>
                <ul class="cart-items">
                    {iteration:widgetMenuAlacarte.items}
                        <li class="item">
                            <div class="item-header">
                                <div class="title">
                                    {$widgetMenuAlacarte.items.title}
                                    {option:widgetMenuAlacarte.items.highlight}
                                        <span class="master-tooltip" title="{$lblHighlightedDescription}"><i class="icon-highlighted" aria-hidden="true"></i></span>
                                    {/option:widgetMenuAlacarte.items.highlight}
                                </div>
                                {option:widgetMenuAlacarte.items.price}<div class="price">&euro; {$widgetMenuAlacarte.items.price}</div>{/option:widgetMenuAlacarte.items.price}
                            </div>
                            <div class="description">
                                {$widgetMenuAlacarte.items.description}
                            </div>
                        </li>
                    {/iteration:widgetMenuAlacarte.items}
                </ul>
            </div>
        {/iteration:widgetMenuAlacarte}
    </div>
{/option:widgetMenuAlacarte}
