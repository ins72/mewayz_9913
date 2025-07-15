<?php
  use Illuminate\View\View;
  use App\Models\LinkShortener;
  use App\Models\LinkShortenerVisitor;

  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('out-shorten-page');
  
  render(function ($slug, View $view) {
    if(!$shorten = LinkShortener::where('slug', $slug)->first()) abort(404);
    
    $ip = getIp(); //getIp() 102.89.2.139
    $tracking = tracking_log();
    if ($vistor = LinkShortenerVisitor::where('session', Session::getId())->where('link_id', $shorten->id)->first()) {
      $vistor->views = ($vistor->views + 1);
      $vistor->save();
    }else{
      $new = new LinkShortenerVisitor;
      $new->link_id = $shorten->id;
      $new->session = \Session::getId();
      $new->ip = $ip;
      $new->tracking = $tracking;
      $new->views = 1;
      $new->save();
    }

    return redirect($shorten->link);
    // return $view->with('service', $service);
  });
?>
