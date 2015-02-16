{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblAlacarte|ucfirst}
    </h2>
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add_alacarte'}" class="button icon iconAdd" title="{$lblAddDish|ucfirst}">
            <span>{$lblAddDish|ucfirst}</span>
        </a>
    </div>
</div>

{option:dataGrids}
{iteration:dataGrids}
    <div class="dataGridHolder" id="dataGrid-{$dataGrids.id}">
        <div class="tableHeading clearfix">
            <h3>{$dataGrids.title}</h3>
        </div>
        {option:dataGrids.content}
        {$dataGrids.content}
        {/option:dataGrids.content}

        {option:!dataGrids.content}
            <p>{$msgNoItems}</p>
        {/option:!dataGrids.content}
    </div>
{/iteration:dataGrids}
{/option:dataGrids}

{option:!dataGrids}
    <p>{$msgNoItems}</p>
{/option:!dataGrids}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
