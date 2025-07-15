
<?php
   use function Livewire\Volt\{state, mount, placeholder, on};
   // placeholder(function(){
   //      return '<div class="p-12 flex justify-center"><div class="container"><div class="loading"><i></i><i></i><i></i><i></i></div></div></div>';
   //  });

   state([
      'item',
   ]);

   // mount(fn () => sleep(20));
?>

<div>
   <livewire:site.generate lazy :site="$item" :key="uukey('site-page', 'siteee-' . $item->_slug)" />
</div>