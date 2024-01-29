<div class="row">
    <h2 class="text-center">Featured categories</h2>
</div>
<div class="row">
    {foreach from=$datas item=categ}
        {assign var=url value=Category::getLinkRewrite($categ['id_category'],1)}
        {assign var=link value=Context::getContext()->link->getCategoryLink($categ['id_category'],$url)}
        <div class="col-md-4"><a href="{$link}">{$categ.name}</a></div>
    {/foreach}
</div>
