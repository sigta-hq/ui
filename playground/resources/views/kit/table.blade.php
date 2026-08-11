<x-kit-shell title="Table">
    <x-kit-example label="Data table — mono caption and headers, tabular numerals">
    «sigta:table caption="Recent engagements">
        «sigta:table.columns>
            «sigta:table.column>Project«/sigta:table.column>
            «sigta:table.column>Stage«/sigta:table.column>
            «sigta:table.column>Margin«/sigta:table.column>
        «/sigta:table.columns>
        «sigta:table.rows>
            «sigta:table.row>
                «sigta:table.cell>Marginalia v3«/sigta:table.cell>
                «sigta:table.cell>«sigta:pill variant="success">Shipping«/sigta:pill>«/sigta:table.cell>
                «sigta:table.cell>62%«/sigta:table.cell>
            «/sigta:table.row>
            «sigta:table.row>
                «sigta:table.cell>Halyard OS«/sigta:table.cell>
                «sigta:table.cell>«sigta:pill variant="success">Active«/sigta:pill>«/sigta:table.cell>
                «sigta:table.cell>41%«/sigta:table.cell>
            «/sigta:table.row>
            «sigta:table.row>
                «sigta:table.cell>Ledger rebuild«/sigta:table.cell>
                «sigta:table.cell>«sigta:pill variant="danger">At risk«/sigta:pill>«/sigta:table.cell>
                «sigta:table.cell>28%«/sigta:table.cell>
            «/sigta:table.row>
        «/sigta:table.rows>
    «/sigta:table>
    </x-kit-example>

    <x-kit-example label="Without caption">
    «sigta:table>
        «sigta:table.columns>
            «sigta:table.column>Metric«/sigta:table.column>
            «sigta:table.column>Value«/sigta:table.column>
        «/sigta:table.columns>
        «sigta:table.rows>
            «sigta:table.row>
                «sigta:table.cell>Projects shipped«/sigta:table.cell>
                «sigta:table.cell>34«/sigta:table.cell>
            «/sigta:table.row>
            «sigta:table.row>
                «sigta:table.cell>Avg. margin«/sigta:table.cell>
                «sigta:table.cell>44%«/sigta:table.cell>
            «/sigta:table.row>
        «/sigta:table.rows>
    «/sigta:table>
    </x-kit-example>
</x-kit-shell>
