
<?php

   use App\Livewire\Actions\Logout;
   use function Livewire\Volt\{on};

   on([

      'logout' => function(Logout $logout){
        $logout();
        $this->redirect('/', navigate: false);
      },
   ]);
?>
<div></div>
