@extends('app')

@section('scripts')
<script src="{{ asset('/js/paypal-payment.js') }}"></script>
<script>
tippy('.tippy', {
  content: 'Tooltip',
  arrow: true,
})
</script>
@endsection