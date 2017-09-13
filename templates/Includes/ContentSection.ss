<%-- Please theme this template --%>

<div class="clearfix $NumberOfColumns" id="content-area">

    <main>
        <div class="typography content-padding">
            <div id="MainContentSection">
                <h1 id="main-page-title">$Title</h1>
                $Content
            </div>
        </div>
    </main>

    <% if $HasSideBar %>
    <aside id="Sidebar" >
        <div class="typography content-padding">
            <% include Sidebar %>
            <% if $MySidebarImage %>
                <div id="MySidebarImage">
                    <img src="$MySidebarImage.Link" alt="$MySidebarImage.Title" />
                </div>
            <% end_if %>
        </div>
    </aside>
    <% end_if %>


    <% if $HasFullWidthContent %>
    <section class="clearfix full-width-content row">
        $FullWidthContent
    </section>
    <% end_if %>
</div>
