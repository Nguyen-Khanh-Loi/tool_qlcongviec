<script>
import { optionChart } from "@/js/helpers/chart";
import Router from '@/js/router';
import {
    authMethods,
    authGetters
} from "@/js/store/helpers";
export default {
    props: {
        results: {
            type: Object,
            default: () => {
                return {}
            },
        },
        id: {
            type: Number,
            default: () => {
                return ;
            },
        },
        slug: {
            type: String,
            default: () => {
                return ;
            },
        },
    },
    data() {
        return {
            series: [],
            chartOption: [],
            data: {}
        };
    },
    computed: { 
        ...authGetters, 
    },
    methods: {
        ...authMethods,
        onViewProject(id){
            if (this.authUserData.roles[0].name === 'administrator') {
                Router.push({ name: 'project', params: { id: this.id } });
                this.$store.dispatch( 'department', id )
            }
        },
    },
    watch: {
        results: function() {
            this.data = {};
            this.data = this.$props.results;
            if (this.data) {
                for (const key in this.data) {
                    var _data = this.data[key];
                    var color = '#042ecb';
                    var title = 'Progress';
                    if (_data.percent == 100) {
                        color = '#7bd405';
                        title = 'Success'
                    }
                    this.series[key] = [_data.percent]; 
                    var option = optionChart.option();
                    option['labels'] = [title];
                    option['colors'] = [color];
                    this.chartOption.push(option);                
                }
            }            
        }
    },
    async created() {
        this.auth();
    }
    
};
</script>
<template>
    <div class="row">  
        <div class="col-md-4" v-for="(result, index) in results" :key="index">
            <div class="card" :role="`${authUserData.roles[0].name === 'administrator' ? 'button' : ''}`" @click="onViewProject(result.id)">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body overflow-hidden">
                            <h4 class="card-title text-truncate font-size-14 mb-2">{{result.title}}</h4>
                            <apexchart
                            type="radialBar"                                        
                            dir="ltr"
                            :series="series[index]"
                            :options="chartOption[index]"
                            ></apexchart>
                        </div>
                        <div class="text-primary">
                            <i :class="`ri-stack-line font-size-24`"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top py-3">
                    <div class="text-truncate">
                        <span class="badge badge-soft-success font-size-11">
                            {{ 100 - result.percent+'%' }}
                        </span>
                        <span class="text-muted ml-2">Chưa hoàn thành</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>