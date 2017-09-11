asdfasdfasdf
<% if $MyMenuItems %>
<ul class="my-menu-items">
    <% if $HasParent %><li><a href="$Link" class="has-parent" data-id="$Parent.ID">$MenuTitle</a></li><% end_if %>
    <% loop $MyMenuItems %>
        <li class="$LinkingMode $FirstLast">
            <a href="$Link">$MenuTitle</a>
            <% if $ChildrenShowInMenu %><a href="$Link" class="has-children" data-id="$ID">$MenuTitle</a><% end_if %>
        </li>
    <% end_loop %>
</ul>
<% end_if %>
