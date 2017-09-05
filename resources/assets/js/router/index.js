import Vue from 'vue';
import Router from 'vue-router';
const _import = require('./_import_' + process.env.NODE_ENV);
// in development env not use Lazy Loading,because Lazy Loading large page will cause webpack hot update too slow.so only in production use Lazy Loading

Vue.use(Router);

/* layout */
import Layout from '../views/layout/Layout';

/**
* icon : the icon show in the sidebar
* hidden : if `hidden:true` will not show in the sidebar
* redirect : if `redirect:noredirect` will no redirct in the levelbar
* noDropdown : if `noDropdown:true` will has no submenu
* meta : { role: ['admin'] }  will control the page role
**/
export const constantRouterMap = [
    { path: '/login', component: _import('login/index'), hidden: true },
    { path: '/authredirect', component: _import('login/authredirect'), hidden: true },
    { path: '/404', component: _import('error/404'), hidden: true },
    { path: '/401', component: _import('error/401'), hidden: true },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    name: '首页',
    hidden: true,
    children: [{ path: 'dashboard', component: _import('dashboard/index') }]
  },
  {
    path: '/introduction',
    component: Layout,
    redirect: '/introduction/index',
    icon: 'xinrenzhinan',
    noDropdown: true,
    children: [{ path: 'index', component: _import('introduction/index'), name: '简述' }]
  }
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
});

export const asyncRouterMap = [
  {
    path: '/finance',
    // beforeEnter: requireAuth,
    component: Layout,
    redirect: 'noredirect',
    name: '财务收款',
    icon: 'tubiaoleixingzhengchang',
    meta: { role: ['admin', 'finance'] },
    children: [
      { path: 'main-order', component: _import('finance/mainOrder'), name: '收款登记表' },
      { path: 'sub-order', component: _import('finance/subOrder'), name: '收款登记表-子订单' },
      { path: 'order-goods-payments', component: _import('finance/orderGoodsPayments'), name: '配送费明细' },
      { path: 'exchange-bottles', component: _import('finance/exchangeBottles'), name: '换盖金额汇总表' },
      { path: 'goods-settings', component: _import('finance/goodsSetting'), name: '货品配送费设置' },
      { path: 'delivery-man', component: _import('finance/deliveryMan'), name: '司机信息表' }
    ]
  },
  {
    path: '/predeposit',
    component: Layout,
    redirect: 'noredirect',
    name: '预存款',
    icon: 'quanxian',
    meta: { role: ['admin', 'finance'] },
    children: [
      { path: 'charge', component: _import('predeposit/charge'), name: '预存款充值' },
      { path: 'detail', component: _import('predeposit/detail'), name: '预存款收支明细' },
      { path: 'report', component: _import('predeposit/report'), name: '预存款收发存汇总表' }
    ]
  },
  {
    path: '/theme',
    component: Layout,
    redirect: 'noredirect',
    name: 'theme',
    icon: 'theme',
    noDropdown: true,
    children: [{ path: 'index', component: _import('theme/index'), name: '换肤' }]
  },

  { path: '*', redirect: '/404', hidden: true }
];

// function requireAuth (to, from, next) {
//   if (window.User) {
//     return next()
//   } else {
//     return next('/')
//   }
// }
