{ajaxheader ui=true}
{pageaddvarblock}
<script type="text/javascript">
    document.observe("dom:loaded", function() {
        Zikula.UI.Tooltips($$('.tooltips'));
    });
</script>
{/pageaddvarblock}

{adminheader}
<div id="top" class="z-admin-content-pagetitle">
    {icon type="cubes" size="small"}
    <h3>{gt text="Category registry"}</h3>
</div>

{gt text="Choose category" assign=chooseCategory}
{gt text="Choose module" assign=chooseModule}
{gt text="Choose entity" assign=chooseEntity}
<form class="z-form" action="{modurl modname="CategoriesModule" type="adminform" func="editregistry"}" method="post" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <table class="z-datatable">
            <thead>
                <tr>
                    <th>{gt text="Module"}</th>
                    <th>{gt text="Entity"}</th>
                    <th>{gt text="Property name"}</th>
                    <th>{gt text="Category"}</th>
                    <th class="z-right">{gt text="Actions"}</th>
                </tr>
            </thead>
            <tbody>
                {foreach item=obj from=$objectArray}
                <tr class="{cycle values=z-odd,z-even}">
                    {if ($obj.id == $id)}
                    <input id="category_registry_id" name="category_registry[id]" value="{$obj.id}" type="hidden" />
                    <td>{selector_module name="category_registry[modname]" selectedValue=$obj.modname defaultValue="" defaultText="$chooseModule" submit="1"}</td>
                    <td>{if $obj.modname}{selector_module_tables modname=$obj.modname name="category_registry[entityname]" selectedValue=$obj.entityname defaultValue="" defaultText=$chooseEntity}{else}----------{/if}</td>
                    <td><input id="category_registry_property" name="category_registry[property]" value="{$obj.property}" type="text" size="20" maxlength="32" /></td>
                    <td>{selector_category category=$root_id name="category_registry[category_id]" includeLeaf=0 selectedValue=$obj.category_id editLink=0}</td>
                    <td>&nbsp;</td>
                    {else}
                    {modgetinfo assign="dModname" info=displayname modname=$obj.modname default=$obj.modname}
                    <td>{$dModname}</td>
                    <td>{$obj.entityname}</td>
                    <td>{$obj.property}</td>
                    <td>{category_path id=$obj.category_id html=true}</td>
                    <td class="z-right">
                        <a href="{modurl modname='CategoriesModule' type='admin' func='editregistry' id=$obj.id}">{img modname=core set=icons/extrasmall src="xedit.png" __title="Edit" __alt="Edit" class="tooltips"}</a>
                        <a href="{modurl modname='CategoriesModule' type='admin' func='deleteregistry' id=$obj.id}">{img modname=core set=icons/extrasmall src="14_layer_deletelayer.png" __title="Delete" __alt="Delete" class="tooltips"}</a>
                    </td>
                    {/if}
                </tr>
                {/foreach}

                {if (!$id)}
                <tr class="{cycle values=z-odd,z-even}" valign="middle">
                    <td><span class="z-form-mandatory-flag">*</span>{selector_module name="category_registry[modname]" defaultValue="0" defaultText=$chooseModule selectedValue=$newobj.modname submit="1"}</td>
                    <td>{if $newobj.modname}<span class="z-form-mandatory-flag">*</span>{selector_module_tables modname=$newobj.modname name="category_registry[entityname]" displayField="name" selectedValue=$newobj.entityname defaultValue="" defaultText=$chooseEntity}{else}----------{/if}</td>
                    <td><span class="z-form-mandatory-flag">*</span><input id="category_registry_property" name="category_registry[property]" value="{$newobj.property|default:'Main'}" type="text" size="20" maxlength="32" /></td>
                    <td><span class="z-form-mandatory-flag">*</span>{selector_category category=$root_id name="category_registry[category_id]" includeLeaf=0 selectedValue=newobj.category_id defaultValue=0 defaultText=$chooseCategory editLink=0}</td>
                    <td>&nbsp;</td>
                </tr>
                {/if}

            </tbody>
        </table>
        <div class="z-buttons z-formbuttons">
            {button id="category_submit" name="category_submit" value="1" src=button_ok.png set=icons/extrasmall __alt="Save" __title="Save" __text="Save"}
            <a href="{modurl modname="CategoriesModule" type="admin" func="editregistry"}" title="{gt text="Cancel"}">{img modname=core src=button_cancel.png set=icons/extrasmall __alt="Cancel" __title="Cancel"} {gt text="Cancel"}</a>
        </div>
    </div>
</form>
{adminfooter}