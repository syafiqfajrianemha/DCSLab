import SideMenu from "@/layouts/side-menu/Main.vue";

import Cash from "@/views/cash/Cash.vue";
import Capital from "@/views/capital/Capital.vue";
import CapitalGroup from "@/views/capital_group/CapitalGroup.vue";
import Expense from "@/views/expense/Expense.vue";
import ExpenseGroup from "@/views/expense_group/ExpenseGroup.vue";
import Income from "@/views/income/Income.vue";
import IncomeGroup from "@/views/income_group/IncomeGroup.vue";
import Investor from "@/views/investor/Investor.vue";

const root = '/dashboard';

export default {
    path: root + '/cash',
    component: SideMenu,
    children: [
        {
            path: root + '/cash' + '/cash',
            name: 'side-menu-cash-cash',
            component: Cash,
            meta: {
                remember: true,
                log_route: true 
            }
        },
        {
            path: root + '/cash' + '/capital' + '/capital',
            name: 'components.menu.cash-capital',
            component: Capital,
            meta: { 
                remember: true,
                log_route: true 
            }
        },
        {
            path: root + '/cash' + '/capital' + '/capital_group',
            name: 'side-menu-cash-capital-capital_group',
            component: CapitalGroup,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/expense' + '/expense',
            name: 'side-menu-cash-expense',
            component: Expense,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/expense' + '/expense_group',
            name: 'side-menu-cash-expense-expense_group',
            component: ExpenseGroup,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/income' + '/income',
            name: 'side-menu-cash-income-income',
            component: Income,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/income' + '/income_group',
            name: 'side-menu-cash-income-income_group',
            component: IncomeGroup,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/investor',
            name: 'side-menu-cash-investor',
            component: Investor,
            meta: {
                remember: true,
                log_route: true
            }
        },
    ]
};