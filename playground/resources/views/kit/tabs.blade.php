<x-kit-shell title="Tabs">
    <x-kit-example label="Tab group">
    «sigta:tabs default="overview">
        «sigta:tabs.list label="Sections">
            «sigta:tabs.tab name="overview">Overview«/sigta:tabs.tab>
            «sigta:tabs.tab name="trends">Trends«/sigta:tabs.tab>
            «sigta:tabs.tab name="reports">Reports«/sigta:tabs.tab>
        «/sigta:tabs.list>
        «sigta:tabs.panel name="overview">«sigta:text>The overview panel — keyboard: arrow keys move between tabs.«/sigta:text>«/sigta:tabs.panel>
        «sigta:tabs.panel name="trends">«sigta:text>Trends panel.«/sigta:text>«/sigta:tabs.panel>
        «sigta:tabs.panel name="reports">«sigta:text>Reports panel.«/sigta:text>«/sigta:tabs.panel>
    «/sigta:tabs>
    </x-kit-example>
</x-kit-shell>
