{*
	variables that are available:
	- {$widgetFaqCategoryList}: contains an array with all posts for the category, each element contains data about the post
*}

{option:widgetMenu}
    <section class="menu-widget-holder">
        <div class="inner">
            <div class="menu-widget">
                <header>
                    <h3>{$widgetMenu.title}</h3>
                </header>
                <div class="bd content">
                    {$widgetMenu.description}
                </div>
                <div class="price">
                    {$widgetMenu.price}
                </div>
            </div>
        </div>
    </section>
{/option:widgetMenu}