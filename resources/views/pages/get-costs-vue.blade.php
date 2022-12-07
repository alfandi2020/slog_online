@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
@extends('layouts.app')

@section('title', trans('nav_menu.get_costs'))

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Cek Ongkir</div>

                <div class="panel-body">
                    <form method="POST" action="#" @submit.prevent="onSubmit" @change="form.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="orig_prov_id" class="control-label">{{ trans('rate.orig_prov_id') }}</label>
                                    <select name="orig_prov_id" class="form-control" @change="getCitiesList('origin')" v-model="form.orig_prov_id">
                                        <option value="">-- Pilih Provinsi --</option>
                                        <option v-for="(province, id) in provinces" :value="id" v-text="province"></option>
                                    </select>
                                    <span class="small text-danger" v-if="form.errors.has('orig_prov_id')" v-text="form.errors.get('orig_prov_id')"></span>
                                </div>
                                <div class="form-group">
                                    <label for="orig_city_id" class="control-label">{{ trans('rate.orig_city_id') }}</label>
                                    <select name="orig_city_id" class="form-control" @change="getDistrictsList('origin')" v-model="form.orig_city_id">
                                        <option value="">-- Pilih Kota/Kab. --</option>
                                        <option v-for="(city, id) in orig_cities" :value="id" v-text='city'></option>
                                    </select>
                                    <span class="small text-danger" v-if="form.errors.has('orig_city_id')" v-text="form.errors.get('orig_city_id')"></span>
                                </div>
                                <div class="form-group">
                                    <label for="orig_city_id" class="control-label">{{ trans('rate.orig_district_id') }}</label>
                                    <select name="orig_district_id" class="form-control" v-model="form.orig_district_id">
                                        <option value="">-- Pilih Kecamatan --</option>
                                        <option v-for="(district, id) in orig_districts" :value="id" v-text='district'></option>
                                    </select>
                                    <span class="small text-danger" v-if="form.errors.has('orig_district_id')" v-text="form.errors.get('orig_district_id')"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="dest_prov_id" class="control-label">{{ trans('rate.dest_prov_id') }}</label>
                                    <select name="dest_prov_id" class="form-control" @change="getCitiesList('destination')" v-model="form.dest_prov_id">
                                        <option value="">-- Pilih Provinsi --</option>
                                        <option v-for="(province, id) in provinces" :value="id" v-text="province"></option>
                                    </select>
                                    <span class="small text-danger" v-if="form.errors.has('dest_prov_id')" v-text="form.errors.get('dest_prov_id')"></span>
                                </div>
                                <div class="form-group">
                                    <label for="dest_city_id" class="control-label">{{ trans('rate.dest_city_id') }}</label>
                                    <select name="dest_city_id" class="form-control" @change="getDistrictsList('destination')" v-model="form.dest_city_id">
                                        <option value="">-- Pilih Kota/Kab. --</option>
                                        <option v-for="(city, id) in dest_cities" :value="id" v-text='city'></option>
                                    </select>
                                    <span class="small text-danger" v-if="form.errors.has('dest_city_id')" v-text="form.errors.get('dest_city_id')"></span>
                                </div>
                                <div class="form-group">
                                    <label for="dest_city_id" class="control-label">{{ trans('rate.dest_district_id') }}</label>
                                    <select name="dest_district_id" class="form-control" v-model="form.dest_district_id">
                                        <option value="">-- Pilih Kecamatan --</option>
                                        <option v-for="(district, id) in dest_districts" :value="id" v-text='district'></option>
                                    </select>
                                    <span class="small text-danger" v-if="form.errors.has('dest_district_id')" v-text="form.errors.get('dest_district_id')"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weight" class="control-label">Weight</label>

                                    <input type="text" id="weight" name="weight" class="form-control" v-model="form.weight" @keydown="form.errors.clear('weight')">

                                    <span class="small text-danger" v-if="form.errors.has('weight')" v-text="form.errors.get('weight')"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <br>
                                <button class="btn btn-info btn-block btn-lg" :disabled="form.errors.any()">Hitung Ongkir</button>
                            </div>
                        </div>


                    </form>
                    <div class="panel panel-default" v-if="dest_details != []">
                        <div class="panel-heading"><h3 class="panel-title">Costs</h3></div>
                        <div class="panel-body">
                            <li>@{{ dest_details.id }}</li>
                            <li>@{{ dest_details.type }}</li>
                            <li>@{{ dest_details.name }}</li>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.15.3/axios.js"></script>
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <script>
        axios.defaults.headers.common['Authorization'] = "Bearer " + '{{ auth()->user()->api_token }}';
    </script>
    <script src="{{ url('js/vue-forms-class.js') }}"></script>
    <script>
        new Vue({
            el: '#app',

            data: {
                form: new Form({
                    orig_prov_id: '',
                    orig_city_id: '',
                    orig_district_id: '',
                    dest_prov_id: '',
                    dest_city_id: '',
                    dest_district_id: '',
                    weight: 1,
                }),
                provinces: [],
                orig_cities: [],
                dest_cities: [],
                orig_districts: [],
                dest_districts: [],
                orig_details: [],
                dest_details: [],
                costs: [],
            },

            mounted() {
                this.getProvincesList();
            },

            methods: {
                onSubmit() {
                    axios.post("{{ route('api.get-costs') }}", this.form.data())
                        .then((response) => {
                            this.orig_details = response.data.destination_details;
                            this.dest_details = response.data.destination_details;
                            this.costs = response.data.costs;
                        })
                        .catch((error) => this.form.onFail(error.response.data));
                },
                getProvincesList() {
                    axios.get("{{ route('api.regions.provinces') }}")
                        .then(response => this.provinces = response.data);
                },
                getCitiesList(type) {
                    var province_id = type == 'origin' ? this.form.orig_prov_id : this.form.dest_prov_id;
                    axios.get("{{ route('api.regions.cities') }}?province_id=" + province_id)
                        .then(response => (type == 'origin') ? this.orig_cities = response.data : this.dest_cities = response.data);
                },
                getDistrictsList(type) {
                    var city_id = type == 'origin' ? this.form.orig_city_id : this.form.dest_city_id;
                    axios.get("{{ route('api.regions.districts') }}?city_id=" + city_id)
                        .then(response => (type == 'origin') ? this.orig_districts = response.data : this.dest_districts = response.data);
                }
            }
        });
    </script>
@endsection