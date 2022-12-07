<li>{!! html_link_to_route('receipts.drafts', trans('nav_menu.receipt_drafts'), [], ['icon' => 'edit']) !!}</li>
<li>{!! html_link_to_route('reports.sales-counter.daily', 'Laporan Penjualan Harian', [], ['icon' => 'line-chart']) !!}</li>
<li>{!! html_link_to_route('invoices.cash.index', trans('cash_invoice.list'), [], ['icon' => 'money']) !!}</li>
<li>{!! html_link_to_route('manifests.handovers.index', trans('manifest.manifest') . ' ' . trans('manifest.handover'), [], ['icon' => 'upload']) !!}</li>
<li>{!! html_link_to_route('manifests.deliveries.index', trans('manifest.manifest') . ' ' . trans('manifest.delivery'), [], ['icon' => 'sign-out']) !!}</li>
<li>{!! html_link_to_route('manifests.distributions.index', trans('manifest.manifest') . ' ' . trans('manifest.distribution'), [], ['icon' => 'truck']) !!}</li>
<li>{!! html_link_to_route('manifests.problems.index', trans('manifest.manifest') . ' ' . trans('manifest.problem'), [], ['icon' => 'exclamation-circle']) !!}</li>