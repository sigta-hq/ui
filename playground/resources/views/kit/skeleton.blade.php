<x-kit-shell title="Skeleton">
    <x-kit-example label="Variants">
    «div class="flex flex-col gap-4 max-w-md">
        «sigta:skeleton variant="heading" />
        «sigta:skeleton />
        «sigta:skeleton class="w-4/5" />
        «sigta:skeleton variant="block" />
        «sigta:skeleton variant="circle" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Composed — card loading state">
    «div class="max-w-md">
        «sigta:card :hover="false">
            «x-slot:thumb>
                «sigta:skeleton variant="block" class="rounded-none size-full" />
            «/x-slot:thumb>
            «div class="flex items-center gap-3 mb-4">
                «sigta:skeleton variant="circle" />
                «div class="flex-1 flex flex-col gap-2">
                    «sigta:skeleton class="w-1/3" />
                    «sigta:skeleton class="w-1/2" />
                «/div>
            «/div>
            «sigta:skeleton variant="heading" />
            «div class="flex flex-col gap-2 mt-3">
                «sigta:skeleton />
                «sigta:skeleton class="w-5/6" />
            «/div>
        «/sigta:card>
    «/div>
    </x-kit-example>
</x-kit-shell>
