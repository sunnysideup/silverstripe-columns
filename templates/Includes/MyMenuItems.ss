<% if $MyMenuItems %>
<ul class="my-menu-items">
    <% if $MyMenuItemsParentPage %><li><a href="$MyMenuItemsParentLink" class="show-parent load-ajax-menu"><i class="material-icons">arrow_upward</i></a></li><% end_if %>
    <% loop $MyMenuItems %>
        <li class="$LinkingMode $FirstLast <% if $ChildrenShowInMenu %>has-children<% end_if %>">
            <% if $ChildrenShowInMenu %><a href="$MyMenuItemsMenuLink" class="show-children load-ajax-menu"><i class="material-icons">arrow_downward</i></a><% end_if %>
            <a href="$Link">$MenuTitle</a>
        </li>
    <% end_loop %>
</ul>
<% end_if %>
