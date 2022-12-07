<li>{!! html_link_to_route('receipts.drafts', trans('nav_menu.receipt_drafts'), [], ['icon' => 'edit']) !!}</li>
<li>{!! html_link_to_route('pickups.index', trans('pickup.pickup'), [], ['icon' => 'truck']) !!}</li>
<li>{!! html_link_to_route('manifests.handovers.index', trans('manifest.manifest') . ' ' . trans('manifest.handover'), [], ['icon' => 'upload']) !!}</li>
<li>{!! html_link_to_route('manifests.deliveries.index', trans('manifest.manifest') . ' ' . trans('manifest.delivery'), [], ['icon' => 'sign-out']) !!}</li>
<li>{!! html_link_to_route('manifests.distributions.index', trans('manifest.manifest') . ' ' . trans('manifest.distribution'), [], ['icon' => 'truck']) !!}</li>
<li>{!! html_link_to_route('manifests.problems.index', trans('manifest.manifest') . ' ' . trans('manifest.problem'), [], ['icon' => 'exclamation-circle']) !!}</li>
