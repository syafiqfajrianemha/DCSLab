<template>
    <AlertPlaceholder :messages="alertErrors" />
    <div class="intro-y" v-if="mode === 'list'">
        <DataList :title="t('views.capital.table.title')" :data="capitalList" v-on:createNew="createNew" v-on:dataListChange="onDataListChange" :enableSearch="true">
           <template v-slot:table="tableProps">
                <table class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">{{ t('views.capital.table.cols.ref_number') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.capital.table.cols.date') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.capital.table.cols.investor') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.capital.table.cols.capital_group') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.capital.table.cols.cash') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.capital.table.cols.capital_status') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.capital.table.cols.amount') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.capital.table.cols.remarks') }}</th>
                            <th class="whitespace-nowrap"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="tableProps.dataList !== undefined" v-for="(item, itemIdx) in tableProps.dataList.data">
                            <tr class="intro-x">
                                <td>{{ item.ref_number }}</td>
                                <td>{{ item.date }}</td>
                                <td>{{ item.investor }}</td>
                                <td>{{ item.capital_group }}</td>
                                <td>{{ item.cash }}</td>
                                <td>{{ item.capital_status }}</td>
                                <td>{{ item.amount }}</td>
                                <td>{{ item.remarks }}</td>
                                <td>
                                    <CheckCircleIcon v-if="item.status === 1" />
                                    <XIcon v-if="item.status === 0" />
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a class="flex items-center mr-3" href="" @click.prevent="showSelected(itemIdx)">
                                            <InfoIcon class="w-4 h-4 mr-1" />
                                            {{ t('components.data-list.view') }}
                                        </a>
                                        <a class="flex items-center mr-3" href="" @click.prevent="editSelected(itemIdx)">
                                            <CheckSquareIcon class="w-4 h-4 mr-1" />
                                            {{ t('components.data-list.edit') }}
                                        </a>
                                        <a class="flex items-center text-danger" href="javascript:;" data-toggle="modal" data-target="#delete-confirmation-modal" @click="deleteSelected(itemIdx)">
                                            <Trash2Icon class="w-4 h-4 mr-1" /> {{ t('components.data-list.delete') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr :class="{'intro-x':true, 'hidden transition-all': expandDetail !== itemIdx}">
                                <td colspan="6">
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.capital.fields.ref_number') }}</div>
                                        <div class="flex-1">{{ item.ref_number }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.capital.fields.date') }}</div>
                                        <div class="flex-1">{{ item.date }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.capital.fields.investor') }}</div>
                                        <div class="flex-1">{{ item.investor }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.capital.fields.capital_group') }}</div>
                                        <div class="flex-1">{{ item.capital_group }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.capital.fields.cash') }}</div>
                                        <div class="flex-1">{{ item.cash }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.capital.fields.capital_status') }}</div>
                                        <div class="flex-1">{{ item.capital_status }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.capital.fields.amount') }}</div>
                                        <div class="flex-1">{{ item.amount }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.capital.fields.remarks') }}</div>
                                        <div class="flex-1">{{ item.remarks }}</div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <Modal :show="deleteModalShow" @hidden="deleteModalShow = false">
                    <ModalBody class="p-0">
                        <div class="p-5 text-center">
                            <XCircleIcon class="w-16 h-16 text-danger mx-auto mt-3" />
                            <div class="text-3xl mt-5">{{ t('components.data-list.delete_confirmation.title') }}</div>
                            <div class="text-slate-600 mt-2">
                                {{ t('components.data-list.delete_confirmation.desc_1') }}<br />{{ t('components.data-list.delete_confirmation.desc_2') }}
                            </div>
                        </div>
                        <div class="px-5 pb-8 text-center">
                            <button type="button" class="btn btn-outline-secondary w-24 mr-1" @click="deleteModalShow = false">
                                {{ t('components.buttons.cancel') }}
                            </button>
                            <button type="button" class="btn btn-danger w-24" @click="confirmDelete">{{ t('components.buttons.delete') }}</button>
                        </div>
                    </ModalBody>
                </Modal>
            </template>
        </DataList>
    </div>

    <div class="intro-y box" v-if="mode !== 'list'">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto" v-if="mode === 'create'">{{ t('views.capital.actions.create') }}</h2>
            <h2 class="font-medium text-base mr-auto" v-if="mode === 'edit'">{{ t('views.capital.actions.edit') }}</h2>
        </div>
        <div class="loader-container">
            <VeeForm id="capitalForm" class="p-5" @submit="onSubmit" @invalid-submit="invalidSubmit" v-slot="{ handleReset, errors }">
                <div class="p-5">                    
                    <!-- #region RefNumber -->
                    <div class="mb-3">
                        <label for="inputRefNumber" class="form-label">{{ t('views.capital.fields.ref_number') }}</label>
                        <div class="flex items-center">
                            <VeeField id="inputRefNumber" name="ref_number" type="text" :class="{'form-control':true, 'border-danger': errors['ref_number']}" :placeholder="t('views.capital.fields.ref_number')" :label="t('views.capital.fields.ref_number')" rules="required" @blur="reValidate(errors)" v-model="capital.ref_number" :readonly="capital.ref_number === '[AUTO]'" />
                            <button type="button" class="btn btn-secondary mx-1" v-show="mode === 'create'">{{ t('components.buttons.auto') }}</button>
                        </div>
                        <ErrorMessage name="ref_number" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Date -->
                    <div class="mb-3">
                        <label for="inputDate" class="form-label">{{ t('views.capital.fields.date') }}</label>
                        <VeeField id="inputDate" name="date" type="text" :class="{'form-control':true, 'border-danger': errors['date']}" :placeholder="t('views.capital.fields.date')" :label="t('views.capital.fields.date')" rules="required" @blur="reValidate(errors)" v-model="capital.date" />
                        <ErrorMessage name="date" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Investor -->
                    <div class="mb-3">
                        <label class="form-label" for="inputCash_id">{{ t('views.branch.fields.investor_id') }}</label>
                        <VeeField as="select" id="investor_id" name="investor_id" :class="{'form-control form-select':true, 'border-danger': errors['investor_id']}" v-model="branch.investor.hId" :label="t('views.branch.fields.investor_id')" rules="required" @blur="reValidate(errors)">
                            <option value="">{{ t('components.dropdown.placeholder') }}</option>
                            <option v-for="c in investorDDL" :value="c.hId">{{ c.name }}</option>
                        </VeeField>
                        <ErrorMessage name="investor_id" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Group -->
                    <div class="mb-3">
                        <label class="form-label" for="inputGroup_id">{{ t('views.branch.fields.capital_group') }}</label>
                        <VeeField as="select" id="capital_group" name="capital_group" :class="{'form-control form-select':true, 'border-danger': errors['capital_group']}" v-model="branch.group.hId" :label="t('views.branch.fields.capital_group')" rules="required" @blur="reValidate(errors)">
                            <option value="">{{ t('components.dropdown.placeholder') }}</option>
                            <option v-for="c in capitalgroupDDL" :value="c.hId">{{ c.name }}</option>
                        </VeeField>
                        <ErrorMessage name="capital_group" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Cash -->
                    <div class="mb-3">
                        <label class="form-label" for="inputCash_id">{{ t('views.branch.fields.cash') }}</label>
                        <VeeField as="select" id="cash" name="cash" :class="{'form-control form-select':true, 'border-danger': errors['cash']}" v-model="branch.investor.hId" :label="t('views.branch.fields.cash')" rules="required" @blur="reValidate(errors)">
                            <option value="">{{ t('components.dropdown.placeholder') }}</option>
                            <option v-for="c in cashDDL" :value="c.hId">{{ c.name }}</option>
                        </VeeField>
                        <ErrorMessage name="cash" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Capital Status -->
                    <div class="mb-3">
                        <label for="capital_status" class="form-label">{{ t('views.capital.fields.capital_status') }}</label>
                        <VeeField as="select" id="capital_status" name="capital_status" :class="{'form-control form-select':true, 'border-danger': errors['capital_status']}" v-model="capital.capital_status" rules="required" @blur="reValidate(errors)">
                            <option value="">{{ t('components.dropdown.placeholder') }}</option>
                            <option v-for="c in capital_statusDDL" :key="c.code" :value="c.code">{{ t(c.name) }}</option>
                        </VeeField>
                        <ErrorMessage name="capital_status" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Amount -->
                    <div class="mb-3">
                        <label for="inputAmount" class="form-label">{{ t('views.capital.fields.amount') }}</label>
                        <textarea id="inputAmount" name="amount" type="number" class="form-control" :placeholder="t('views.capital.fields.amount')" v-model="capital.address" rows="3"></textarea>
                    </div>
                    <!-- #endregion -->

                    <!-- #region City -->
                    <div class="mb-3">
                        <label for="inputCity" class="form-label">{{ t('views.capital.fields.city') }}</label>
                        <input id="inputCity" name="city" type="text" class="form-control" :placeholder="t('views.capital.fields.city')" v-model="capital.city" />
                    </div>
                    <!--  #endregion -->

                    <!-- #region Remarks -->
                    <div class="mb-3">
                        <label for="inputRemarks" class="form-label">{{ t('views.capital.fields.remarks') }}</label>
                        <textarea id="inputRemarks" name="remarks" type="text" class="form-control" :placeholder="t('views.capital.fields.remarks')" v-model="capital.remarks" rows="3"></textarea>
                    </div>
                    <!-- #endregion -->
                </div>
                <div class="pl-5" v-if="mode === 'create' || mode === 'edit'">
                    <button type="submit" class="btn btn-primary w-24 mr-3">{{ t('components.buttons.save') }}</button>
                    <button type="button" class="btn btn-secondary" @click="handleReset(); resetAlertErrors()">{{ t('components.buttons.reset') }}</button>
                </div>
            </VeeForm>
            <div class="loader-overlay" v-if="loading"></div>
        </div>
        <hr/>
        <div>
            <button type="button" class="btn btn-secondary w-15 m-3" @click="backToList">{{ t('components.buttons.back') }}</button>
        </div>
    </div>
</template>

<script setup>
//#region Imports
import { onMounted, onUnmounted, ref, computed, watch } from "vue";
import axios from "@/axios";
import { useI18n } from "vue-i18n";
import { route } from "@/ziggy";
import dom from "@left4code/tw-starter/dist/js/dom";
import { useUserContextStore } from "@/stores/user-context";
import DataList from "@/global-components/data-list/Main";
import AlertPlaceholder from "@/global-components/alert-placeholder/Main";
//#endregion

//#region Declarations
const { t } = useI18n();
//#endregion

//#region Data - Pinia
const userContextStore = useUserContextStore();
const selectedUserCompany = computed(() => userContextStore.selectedUserCompany );
//#endregion

//#region Data - UI
const mode = ref('list');
const loading = ref(false);
const alertErrors = ref([]);
const deleteId = ref('');
const deleteModalShow = ref(false);
const expandDetail = ref(null);
//#endregion

//#region Data - Views
const capitalList = ref({});
const capital = ref({
    ref_number: '',
    date: '',
    investor: {
            hId: '',
            name: ''
        },
    group: {
            hId: '',
            name: ''
        },
    cash: {
            hId: '',
            name: ''
        },
    capital_status: '',
    amount: '',
    remarks: '',
});
const companyDDL = ref([]);
const investorDDL = ref([]);
const groupDDL = ref([]);
const cashDDL = ref([]);
const capital_statusDDL = ref([]);
//#endregion

//#region onMounted
onMounted(() => {
    if (selectedUserCompany.value !== '') {
        getAllCapitals({ page: 1 });
        getDDLSync();
    } else  {
        
    }

    setMode();
    
    getDDL();

    loading.value = false;
});

onUnmounted(() => {
    sessionStorage.removeItem('DCSLAB_LAST_ENTITY');
});
//#endregion

//#region Methods
const setMode = () => {
    if (sessionStorage.getItem('DCSLAB_LAST_ENTITY') !== null) createNew();
}

const getAllCapitals = (args) => {
    capitalList.value = {};
    if (args.pageSize === undefined) args.pageSize = 10;
    if (args.search === undefined) args.search = '';

    let companyId = selectedUserCompany.value;

    axios.get(route('api.get.db.cash.capital.read', { "companyId": companyId, "page": args.page, "perPage": args.pageSize, "search": args.search })).then(response => {
        capitalList.value = response.data;
        loading.value = false;
    });
}

const getDDL = () => {
    if (getCachedDDL('statusDDL') == null) {
        axios.get(route('api.get.db.common.ddl.list.statuses')).then(response => {
            statusDDL.value = response.data;
            setCachedDDL('statusDDL', response.data);
        });    
    } else {
        statusDDL.value = getCachedDDL('statusDDL');
    }
}

const getDDLSync = () => {
    axios.get(route('api.get.db.cash.investor.read.all_active', {
            companyId: selectedUserCompany.value,
            paginate: false
        })).then(response => {
            investorDDL.value = response.data;
    });
    axios.get(route('api.get.db.cash.capital_group.read.all_active', {
            companyId: selectedUserCompany.value,
            paginate: false
        })).then(response => {
            capital_groupDDL.value = response.data;
    });
    axios.get(route('api.get.db.cash.cash.read.all_active', {
            companyId: selectedUserCompany.value,
            paginate: false
        })).then(response => {
            cashDDL.value = response.data;
    });
}

const onSubmit = (values, actions) => {
    loading.value = true;

    var formData = new FormData(dom('#capitalForm')[0]); 
    formData.append('company_id', selectedUserCompany.value);
    
    if (mode.value === 'create') {
        axios.post(route('api.post.db.cash.capital.save'), formData).then(response => {
            backToList();
        }).catch(e => {
            handleError(e, actions);
        }).finally(() => {
            loading.value = false;
        });
    } else if (mode.value === 'edit') {
        axios.post(route('api.post.db.cash.capital.edit', capital.value.hId), formData).then(response => {
            actions.resetForm();
            backToList();
        }).catch(e => {
            handleError(e, actions);
        }).finally(() => {
            loading.value = false;
        });
    } else { }
}

const handleError = (e, actions) => {
    //Laravel Validations
    if (e.response.data.errors !== undefined && Object.keys(e.response.data.errors).length > 0) {
        for (var key in e.response.data.errors) {
            for (var i = 0; i < e.response.data.errors[key].length; i++) {
                actions.setFieldError(key, e.response.data.errors[key][i]);
            }
        }
        alertErrors.value = e.response.data.errors;
    } else {
        //Catch From Controller
        alertErrors.value = {
            controller: e.response.status + ' ' + e.response.statusText +': ' + e.response.data.message
        };
    }
}

const invalidSubmit = (e) => {
    alertErrors.value = e.errors;
    if (dom('.border-danger').length !== 0) dom('.border-danger')[0].scrollIntoView({ behavior: "smooth" });
}

const reValidate = (errors) => {
    alertErrors.value = errors;
}

const emptyCapital = () => {
    return {
        ref_number: '',
        date: '',
        investor: {
                hId: '',
                name: ''
            },
        group: {
                hId: '',
                name: ''
            },
        cash: {
                hId: '',
                name: ''
            },
        capital_status: '',
        amount: '',
        remarks: '',
    }
}

const resetAlertErrors = () => {
    alertErrors.value = [];
}

const createNew = () => {
    mode.value = 'create';
    
    if (sessionStorage.getItem('DCSLAB_LAST_ENTITY') !== null) {
        capital.value = JSON.parse(sessionStorage.getItem('DCSLAB_LAST_ENTITY'));
        sessionStorage.removeItem('DCSLAB_LAST_ENTITY');
    } else {
        capital.value = emptyCapital();
    }
    capital.value.company.hId = _.find(companyDDL.value, { 'hId': selectedUserCompany.value });
}

const onDataListChange = ({page, pageSize, search}) => {
    getAllCapitals({page, pageSize, search});
}

const editSelected = (index) => {
    mode.value = 'edit';
    capital.value = capitalList.value.data[index];
}

const deleteSelected = (index) => {
    deleteId.value = capitalList.value.data[index].hId;
    deleteModalShow.value = true;
}

const confirmDelete = () => {
    deleteModalShow.value = false;
    axios.post(route('api.post.db.cash.capital.delete', deleteId.value)).then(response => {
        backToList();
    }).catch(e => {
        alertErrors.value = e.response.data;
    }).finally(() => {

    });
}

const showSelected = (index) => {
    toggleDetail(index);
}

const backToList = () => {
    resetAlertErrors();
    sessionStorage.removeItem('DCSLAB_LAST_ENTITY');

    mode.value = 'list';
    getAllCapitals({ page: capitalList.value.current_page, pageSize: capitalList.value.per_page });
}

const toggleDetail = (idx) => {
    if (expandDetail.value === idx) {
        expandDetail.value = null;
    } else {
        expandDetail.value = idx;
    }
}

const generateCode = () => {
    if (capital.value.code === '[AUTO]') capital.value.code = '';
    else  capital.value.code = '[AUTO]'
}
//#endregion

//#region Computed
//#endregion

//#region Watcher
watch(selectedUserCompany, () => {
    if (selectedUserCompany.value !== '') {
        getAllCapitals({ page: 1 });
        getDDLSync();
    }
});

watch(capital, (newV) => {
    if (mode.value == 'create') sessionStorage.setItem('DCSLAB_LAST_ENTITY', JSON.stringify(newV));
}, { deep: true });
//#endregion
</script>