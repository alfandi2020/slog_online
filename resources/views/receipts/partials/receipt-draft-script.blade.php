<script>
(function() {
    $.ajaxSetup({
        headers: {
            "Authorization": "Bearer " + '{{ auth()->user()->api_token }}',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.select2').select2();
    $('.time-select').datetimepicker({
        format:'Y-m-d H:i',
        closeOnTimeSelect: true,
        scrollMonth: false
    });

    $('input[type=radio][name=be_insured]').change(function() {
        if (this.value == '0')
            $('#package_value').attr('readonly', true);
        else if (this.value == '1')
            $('#package_value').attr('readonly', false);
    });

    $("#customer_id").change(function(e) {
        var customer_id = $("#customer_id").val();
        if (customer_id == '') return false;

        $.post('{{ route("receipts.get-customer-data") }}', {
                customer_id: customer_id,
                receipt_key: "{{ $receipt->receiptKey }}"
            }, function(data) {
                $("#consignor_name").val(data.name);
                $("input[name='consignor_address[1]']").val(data.address[1]);
                $("input[name='consignor_address[2]']").val(data.address[2]);
                $("input[name='consignor_address[3]']").val(data.address[3]);
                $("#consignor_phone").val(data.pic['phone']);
            }
        );
    });

    $("#dest_city_id").change(function(e) {
        var dest_city_id = $(this).val();
        var type = $(this).attr('id');
        if (dest_city_id == '') return false;
        $.get("{{ route('api.regions.destination-districts') }}", { dest_city_id: dest_city_id, orig_city_id: '{{ $receipt->orig_city_id }}' },
            function(data) {
                var string = '<option value="">-- {{ trans('address.district') }} --</option>';
                $.each(data, function(index, value) {
                    string = string + `<option value="` + index + `">` + value + `</option>`;
                })
                if (type == 'orig_city_id')
                    $("#orig_district_id").html(string);
                else
                    $("#dest_district_id").html(string);
            }
        );
    });

    $("#calculate_charge").click(function(e) {
        var orig_city_id = $("#orig_city_id").val();
        var dest_city_id = $("#dest_city_id").val();
        var service_id = $("input[name=service_id]:checked").val();
        var pcs_count = $("#pcs_count").val();
        var charged_weight = $("#charged_weight").val();

        if (orig_city_id == '' || dest_city_id == ''|| service_id == ''
            || pcs_count == '' || charged_weight == ''
        ) return false;

        var orig_district_id = $("#orig_district_id").val();
        var dest_district_id = $("#dest_district_id").val();
        var customer_id = $("#customer_id").val();
        var items_count = $("#items_count").val();
        var charged_on = $("input[name=charged_on]:checked").val();
        var pack_type_id = $("input[name='pack_type_id']:checked").val();
        var packing_cost = $("#packing_cost").val();
        var add_cost = $("#add_cost").val();
        var admin_fee = $("input[name='admin_fee']:checked").val();
        var be_insured = $("input[name='be_insured']:checked").val();
        var package_value = $("#package_value").val();
        var discount = $("#discount").val();
        var receipt_key = "{{ $receipt->receiptKey }}";

        $.post(
            '{{ route("receipts.get-charge-calculation") }}',
            {
                receipt_key: receipt_key,
                orig_city_id: orig_city_id,
                orig_district_id: orig_district_id,
                dest_city_id: dest_city_id,
                dest_district_id: dest_district_id,
                service_id: service_id,
                customer_id: customer_id,
                pcs_count: pcs_count,
                items_count: items_count,
                charged_on: charged_on,
                pack_type_id: pack_type_id,
                charged_weight: charged_weight,
                packing_cost: packing_cost,
                add_cost: add_cost,
                admin_fee: admin_fee,
                package_value: package_value,
                be_insured: be_insured,
                discount: discount
            },
            function(response) {
                // console.log(response);
                if (response.success == true) {
                    $("#display_base_charge").html(response.display_base_charge);
                    $("#display_discount").html(response.display_discount);
                    $("#display_subtotal").html(response.display_subtotal);
                    $("#display_insurance_cost").html(response.display_insurance_cost);
                    $("#display_packing_cost").html(response.display_packing_cost);
                    $("#display_add_cost").html(response.display_add_cost);
                    $("#display_admin_fee").html(response.display_admin_fee);
                    $("#display_total").html(response.display_total);
                }

                if (response.message != '') {
                    alert(response.message);
                }
            }
        );
    });

    $('#customer_id').change(function(){
        $(this).find(':selected').each(function(){
            var optionValue = $(this).val();
            console.log(optionValue);
            if((optionValue == 37) || (optionValue == 182) || (optionValue == 183) || (optionValue == 184) || (optionValue == 185) || (optionValue == 186) || (optionValue == 187) || (optionValue == 188) || (optionValue == '')){
                $('.payment-type ul li:nth-child(2)').hide();
                $("#consignor_name").attr('readonly',false);
                // $("input[name='consignor_address[1]']").attr('readonly',false);
                // $("input[name='consignor_address[2]']").attr('readonly',false);
                // $("input[name='consignor_address[3]']").attr('readonly',false);
                $('#payment_type_id_1').prop('checked',true);
                $('#payment_type_id_2').prop('checked',false);
                console.log(optionValue);
            } else{
                $('.payment-type ul li:nth-child(2)').show();
                $("#consignor_name").attr('readonly',true);
                // $("input[name='consignor_address[1]']").attr('readonly',true);
                // $("input[name='consignor_address[2]']").attr('readonly',true);
                // $("input[name='consignor_address[3]']").attr('readonly',true);
                $('#payment_type_id_2').prop('checked',true);
                $('#payment_type_id_1').prop('checked',false);
            }
        });
    }).change();

})();
</script>
