<template>
    <AlertPlaceholder :messages="alertErrors" />
    <div class="intro-y" v-if="mode === 'list'">
        <DataList :title="t('views.expense.table.title')" :data="expenseList" v-on:createNew="createNew" v-on:dataListChange="onDataListChange" :enableSearch="true">
           <template v-slot:table="tableProps">
                <table class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.branch_id') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.code') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.date') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.payment_term_type') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.expense_group_id') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.cash_id') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.amount') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.amount_owed') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.remarks') }}</th>
                            <th class="whitespace-nowrap">{{ t('views.expense.table.cols.posted') }}</th>
                            <th class="whitespace-nowrap"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="tableProps.dataList !== undefined" v-for="(item, itemIdx) in tableProps.dataList.data">
                            <tr class="intro-x">
                                <td>{{ item.branch.name }}</td>
                                <td>{{ item.code }}</td>
                                <td>{{ item.date }}</td>
                                <td>{{ item.payment_term_type }}</td>
                                <td>{{ item.expense_group.name }}</td>
                                <td>{{ item.cash.name }}</td>
                                <td>{{ item.amount }}</td>
                                <td>{{ item.amount_owed }}</td>
                                <td>{{ item.remarks }}</td>
                                <td>
                                    <CheckCircleIcon v-if="item.post === 'ACTIVE'" />
                                    <XIcon v-if="item.post === 'INACTIVE'" />
                                </td>
                                <td class="table-report__action w-12">
                                    <div class="flex justify-center items-center">
                                        <Tippy tag="a" href="javascript:;" class="tooltip p-2 hover:border" :content="t('components.data-list.view')" @click.prevent="showSelected(itemIdx)">
                                            <InfoIcon class="w-4 h-4" />
                                        </Tippy>
                                        <Tippy tag="a" href="javascript:;" class="tooltip p-2 hover:border" :content="t('components.data-list.edit')" @click.prevent="editSelected(itemIdx)">
                                            <CheckSquareIcon class="w-4 h-4" />
                                        </Tippy>
                                        <Tippy tag="a" href="javascript:;" class="tooltip p-2 hover:border" :content="t('components.data-list.delete')" @click.prevent="deleteSelected(itemIdx)">
                                            <Trash2Icon class="w-4 h-4 text-danger" />
                                        </Tippy>
                                    </div>
                                </td>
                            </tr>
                            <tr :class="{'intro-x':true, 'hidden transition-all': expandDetail !== itemIdx}">
                                <td colspan="6">
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.expense.fields.branch_id') }}</div>
                                        <div class="flex-1">{{ item.branch.name }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.expense.fields.code') }}</div>
                                        <div class="flex-1">{{ item.code }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.expense.fields.name') }}</div>
                                        <div class="flex-1">{{ item.name }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.expense.fields.remarks') }}</div>
                                        <div class="flex-1">{{ item.remarks }}</div>
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="ml-5 w-48 text-right pr-5">{{ t('views.expense.fields.status') }}</div>
                                        <div class="flex-1">
                                            <span v-if="item.status === 'ACTIVE'">{{ t('components.dropdown.values.statusDDL.active') }}</span>
                                            <span v-if="item.status === 'INACTIVE'">{{ t('components.dropdown.values.statusDDL.inactive') }}</span>
                                        </div>
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
            <h2 class="font-medium text-base mr-auto" v-if="mode === 'create'">{{ t('views.expense.actions.create') }}</h2>
            <h2 class="font-medium text-base mr-auto" v-if="mode === 'edit'">{{ t('views.expense.actions.edit') }}</h2>
        </div>
        <div class="loader-container">
            <VeeForm id="expenseForm" class="p-5" @submit="onSubmit" @invalid-submit="invalidSubmit" v-slot="{ handleReset, errors }">
                <div class="p-5">
                    <!-- #region Branch -->
                    <div class="mb-3">
                        <label class="form-label" for="inputBranchId">{{ t('views.expense.fields.branch') }}</label>
                        <VeeField as="select" id="branch" name="branch" :class="{'form-control form-select':true, 'border-danger': errors['branch']}" v-model="branch.company.hId" :label="t('views.branch.fields.branch')" rules="required" @blur="reValidate(errors)">
                            <option value="">{{ t('components.dropdown.placeholder') }}</option>
                            <option v-for="c in companyDDL" :value="c.hId">{{ c.name }}</option>
                        </VeeField>
                        <ErrorMessage name="branch" class="text-danger" />
                    </div>
                    <!-- #endregion -->
                    
                    <!-- #region Code -->
                    <div class="mb-3">
                        <label for="inputCode" class="form-label">{{ t('views.expense.fields.code') }}</label>
                        <div class="flex items-center">
                            <VeeField id="inputCode" name="code" type="text" :class="{'form-control':true, 'border-danger': errors['code']}" :placeholder="t('views.expense.fields.code')" :label="t('views.expense.fields.code')" rules="required" @blur="reValidate(errors)" v-model="expense.code" :readonly="expense.code === '[AUTO]'" />
                            <button type="button" class="btn btn-secondary mx-1" @click="generateCode" v-show="mode === 'create'">{{ t('components.buttons.auto') }}</button>
                        </div>
                        <ErrorMessage name="code" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Date -->
                    <div class="mb-3">
                        <label for="inputDate" class="form-label">{{ t('views.expense.fields.name') }}</label>
                        <VeeField id="inputDate" name="name" type="text" :class="{'form-control':true, 'border-danger': errors['name']}" :placeholder="t('views.expense.fields.name')" :label="t('views.expense.fields.name')" rules="required" @blur="reValidate(errors)" v-model="expense.name" />
                        <ErrorMessage name="name" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Payment Term Type -->
                    <div class="mb-3">
                        <label for="inputPaymentTermType" class="form-label">{{ t('views.expense.fields.address') }}</label>
                        <textarea id="inputPaymentTermType" name="address" type="text" class="form-control" :placeholder="t('views.expense.fields.address')" v-model="expense.address" rows="3"></textarea>
                    </div>
                    <!-- #endregion -->

                    <!-- #region Expense Group -->
                    <div class="mb-3">
                        <label class="form-label" for="inputExpenseGroupId">{{ t('views.expense.fields.expense') }}</label>
                        <VeeField as="select" id="expense_group" name="expense_group" :class="{'form-control form-select':true, 'border-danger': errors['expense_group']}" v-model="expense_group.company.hId" :label="t('views.expense_group_id.fields.expense_group_id')" rules="required" @blur="reValidate(errors)">
                            <option value="">{{ t('components.dropdown.placeholder') }}</option>
                            <option v-for="c in expense_groupDDL" :value="c.hId">{{ c.name }}</option>
                        </VeeField>
                        <ErrorMessage name="expense_group_id" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Cassh -->
                    <div class="mb-3">
                        <label class="form-label" for="inputCashId">{{ t('views.expense.fields.cash') }}</label>
                        <VeeField as="select" id="cash" name="cash" :class="{'form-control form-select':true, 'border-danger': errors['cash']}" v-model="cash.company.hId" :label="t('views.cash.fields.cash')" rules="required" @blur="reValidate(errors)">
                            <option value="">{{ t('components.dropdown.placeholder') }}</option>
                            <option v-for="c in companyDDL" :value="c.hId">{{ c.name }}</option>
                        </VeeField>
                        <ErrorMessage name="cash" class="text-danger" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Amount -->
                    <div class="mb-3">
                        <label for="inputAmount" class="form-label">{{ t('views.expense.fields.amount') }}</label>
                        <input id="inputAmount" name="amount" type="text" class="form-control" :placeholder="t('views.expense.fields.amount')" v-model="expense.city" />
                    </div>
                    <!--  #endregion -->

                    <!-- #region Amount Owed -->
                    <div class="mb-3">
                        <label for="inputAmountOwed" class="form-label">{{ t('views.expense.fields.amount_owed') }}</label>
                        <input id="inputAmountOwed" name="amount_owed" type="text" class="form-control" :placeholder="t('views.expense.fields.amount_owed')" v-model="expense.contact" />
                    </div>
                    <!-- #endregion -->

                    <!-- #region Remarks -->
                    <div class="mb-3">
                        <label for="inputRemarks" class="form-label">{{ t('views.expense.fields.remarks') }}</label>
                        <textarea id="inputRemarks" name="remarks" type="text" class="form-control" :placeholder="t('views.expense.fields.remarks')" v-model="expense.remarks" rows="3"></textarea>
                    </div>
                    <!-- #endregion -->

                    <!-- #region Posted -->
                    <div class="mb-3">
                        <label for="posted" class="form-label">{{ t('views.expense.fields.posted') }}</label>
                        <VeeField as="select" id="posted" name="posted" :class="{'form-control form-select':true, 'border-danger': errors['posted']}" v-model="expense.posted" rules="required" @blur="reValidate(errors)">
                            <option value="">{{ t('components.dropdown.placeholder') }}</option>
                            <option v-for="c in postedDDL" :key="c.code" :value="c.code">{{ t(c.name) }}</option>
                        </VeeField>
                        <ErrorMessage name="posted" class="text-danger" />
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
import { getCachedDDL, setCachedDDL } from "@/mixins";
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
const expenseList = ref({});
const expense = ref({
    company: { 
        hId: '',
    },
    branch: { 
        hId: '',
        name: ''
    },
    code: '',
    date: '',
    payment_term_type: '',
    expense_group: { 
        hId: '',
        name: ''
    },
    cash: { 
        hId: '',
        name: ''
    },
    amount: '',
    amount_owed: '',
    remarks: '',
    posted: 'ACTIVE',
});
const branchDDL = ref([]);
const expense_groupDDL = ref([]);
const cashDDL = ref([]);
const postedDDL = ref([]);
//#endregion

//#region onMounted
onMounted(() => {
    if (selectedUserCompany.value !== '') {
        getAllExpenses({ page: 1 });
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

const getAllExpenses = (args) => {
    expenseList.value = {};
    if (args.pageSize === undefined) args.pageSize = 10;
    if (args.search === undefined) args.search = '';

    let companyId = selectedUserCompany.value;

    axios.get(route('api.get.db.cash.expense.read', { "companyId": companyId, "page": args.page, "perPage": args.pageSize, "search": args.search })).then(response => {
        expenseList.value = response.data;
        loading.value = false;
    });
}

const getDDL = () => {
    if (getCachedDDL('postedDDL') == null) {
        axios.get(route('api.get.db.common.ddl.list.posted')).then(response => {
            postedDDL.value = response.data;
            setCachedDDL('postedDDL', response.data);
        });    
    } else {
        postedDDL.value = getCachedDDL('postedDDL');
    }
}

const getDDLSync = () => {
    axios.get(route('api.get.db.company.branch.read.all_active', {
            companyId: selectedUserCompany.value,
            paginate: false
        })).then(response => {
            branchDDL.value = response.data;
    });
    axios.get(route('api.get.db.cash.cash.read.all_active', {
            companyId: selectedUserCompany.value,
            paginate: false
        })).then(response => {
            cashyDDL.value = response.data;
    });
    axios.get(route('api.get.db.cash.investor.read.all_active', {
            companyId: selectedUserCompany.value,
            paginate: false
        })).then(response => {
            investorDDL.value = response.data;
    });
}

const onSubmit = (values, actions) => {
    loading.value = true;

    var formData = new FormData(dom('#expenseForm')[0]); 
    formData.append('company_id', selectedUserCompany.value);
    
    if (mode.value === 'create') {
        axios.post(route('api.post.db.cash.expense.save'), formData).then(response => {
            backToList();
        }).catch(e => {
            handleError(e, actions);
        }).finally(() => {
            loading.value = false;
        });
    } else if (mode.value === 'edit') {
        axios.post(route('api.post.db.cash.expense.edit', expense.value.hId), formData).then(response => {
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

const emptyExpense = () => {
    return {
        company: { 
        hId: '',
        },
        branch: { 
            hId: '',
            name: ''
        },
        code: '',
        date: '',
        payment_term_type: '',
        expense_group: { 
            hId: '',
            name: ''
        },
        cash: { 
            hId: '',
            name: ''
        },
        amount: '',
        amount_owed: '',
        remarks: '',
        posted: 'ACTIVE',
    }
}

const resetAlertErrors = () => {
    alertErrors.value = [];
}

const createNew = () => {
    mode.value = 'create';
    
    if (sessionStorage.getItem('DCSLAB_LAST_ENTITY') !== null) {
        expense.value = JSON.parse(sessionStorage.getItem('DCSLAB_LAST_ENTITY'));
        sessionStorage.removeItem('DCSLAB_LAST_ENTITY');
    } else {
        expense.value = emptyExpense();
    }
    expense.value.company.hId = _.find(companyDDL.value, { 'hId': selectedUserCompany.value });
}

const onDataListChange = ({page, pageSize, search}) => {
    getAllExpenses({page, pageSize, search});
}

const editSelected = (index) => {
    mode.value = 'edit';
    expense.value = expenseList.value.data[index];
}

const deleteSelected = (index) => {
    deleteId.value = expenseList.value.data[index].hId;
    deleteModalShow.value = true;
}

const confirmDelete = () => {
    deleteModalShow.value = false;
    axios.post(route('api.post.db.cash.expense.delete', deleteId.value)).then(response => {
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
    getAllExpenses({ page: expenseList.value.current_page, pageSize: expenseList.value.per_page });
}

const toggleDetail = (idx) => {
    if (expandDetail.value === idx) {
        expandDetail.value = null;
    } else {
        expandDetail.value = idx;
    }
}

const generateCode = () => {
    if (expense.value.code === '[AUTO]') expense.value.code = '';
    else  expense.value.code = '[AUTO]'
}
//#endregion

//#region Computed
//#endregion

//#region Watcher
watch(selectedUserCompany, () => {
    if (selectedUserCompany.value !== '') {
        getAllExpenses({ page: 1 });
        getDDLSync();
    }
});

watch(expense, (newV) => {
    if (mode.value == 'create') sessionStorage.setItem('DCSLAB_LAST_ENTITY', JSON.stringify(newV));
}, { deep: true });
//#endregion
</script>