<template>
  <el-form :model="mainOrder" ref="orderMainForm">
    <el-row>
        <h3 class="form-title">订单信息</h3>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="支付单号">
            <span class="form-item">
              {{mainOrder.pay_sn}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="订单时间">
            <span class="form-item">
              {{mainOrder.add_time}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="门店名称">
            <span class="form-item">
              {{mainOrder.receiver_shop_name}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="收货人">
            <span class="form-item">
              {{mainOrder.receiver_name}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="16">
          <el-form-item label="详细地址">
            <span class="form-item">
              {{mainOrder.receiver_area_info}} {{mainOrder.receiver_address_detail}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>
    <el-row>
      <span class="form-line-head">实发金额{{mainOrder.shifa}}</span><span class="form-line-head form-line-head-desc">（备注实发=货品金额-缺货-拒收）</span>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="货品金额">
            <span class="form-item">
              {{mainOrder.receiver_name}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <el-form-item label="缺货金额">
            <span class="form-item">
              {{mainOrder.quehuo}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <el-form-item label="拒收金额">
            <span class="form-item">
              {{mainOrder.jushou}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="4">
            <el-button @click="toogleGoodsList()">缺货/拒收录入</el-button>
        </el-col>
    </el-row>
    <el-row>
      <order-goods-list :goodsList="mainOrder.goods_list" @goods-change="handleGoodsListChange"
        v-if="goodsListVisible">
      </order-goods-list>
    </el-row>

    <el-row>
      <span class="form-line-head">应收金额{{mainOrder.yingshou}}</span><span class="form-line-head form-line-head-desc">（备注：应收=实发金额-签单-自提-其他-尾差）</span>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="签单金额：">
            <el-input placeholder="签单金额" v-model="mainOrder.qiandan">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="自提金额：">
            <el-input placeholder="自提金额" v-model="mainOrder.ziti">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="尾差金额：">
            <el-input placeholder="尾差金额" v-model="mainOrder.weicha">
            </el-input>
          </el-form-item>
        </el-col>

        <el-col :span="8">
          <el-form-item label="其他金额：">
            <el-input placeholder="其他金额" v-model="mainOrder.qita">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="备注：">
            <el-input type="textarea" :autosize="{ minRows: 1, maxRows: 4}"
              placeholder="扣减说明" v-model="mainOrder.desc_remark">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="代金券：">
            <span class="form-item">
              {{mainOrder.promotion_amount}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>

    <el-row>
      <span  class="form-line-head">实收金额{{mainOrder.shishou}}</span><span class="form-line-head form-line-head-desc">（备注：实收=预存款+POS+微信+支付宝+现金）</span>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="8">
        <el-form-item label="预存款金额：">
          <span class="form-item">
            {{mainOrder.pd_amount}}
          </span>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="POS金额：">
          <el-input placeholder="POS金额" v-model="mainOrder.qiandan">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="刷卡单号：">
          <el-input placeholder="刷卡单号" v-model="mainOrder.out_pay_sn">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="微信金额：">
          <el-input placeholder="微信金额" v-model="mainOrder.weixin">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="支付宝金额：">
          <el-input placeholder="支付宝金额" v-model="mainOrder.alipay">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="现金：">
          <el-input placeholder="现金" v-model="mainOrder.cash">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="翼支付金额：">
          <el-input placeholder="翼支付金额" v-model="mainOrder.yizhifu">
          </el-input>
        </el-form-item>
      </el-col>

      <el-col :span="8">
        <el-form-item label="收款日期：">
          <el-date-picker
            v-model="mainOrder.jk_at"
            type="datetime"
            placeholder="选择日期时间"
            align="right"
            :picker-options="dateOptions">
          </el-date-picker>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="存款日期：">
          <el-date-picker
            v-model="mainOrder.ck_at"
            type="datetime"
            placeholder="选择日期时间"
            align="right"
            :picker-options="dateOptions">
          </el-date-picker>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="收款备注：">
          <el-input type="textarea" :autosize="{ minRows: 1, maxRows: 4}"
            v-model="mainOrder.remark">
          </el-input>
        </el-form-item>
      </el-col>
    </el-row>
    <el-row>
      <span class="form-line-head">配送费{{mainOrder.shishou}}</span>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="12">
        <el-form-item label="货物配送费">
          <el-input placeholder="货物配送费" v-model="mainOrder.shipping_fee">
            <el-button slot="append" @click="handleReCalculate()">重算</el-button>
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="司机配送费">
          <el-input placeholder="司机配送费" v-model="mainOrder.driver_fee">
            <el-button slot="append" @click="handleReCalculate()">重算</el-button>
          </el-input>
        </el-form-item>
      </el-col>
      <el-row>
          <el-col :span="6">
              <el-checkbox v-model="mainOrder.sencod_driver_id" :checked="mainOrder.sencod_driver_id">二次配送</el-checkbox>
          </el-col>
          <el-col :span="18">
            <div>
              <span>首次配送司机</span>
              <el-select
                v-model="mainOrder.driver_id"
                filterable
                placeholder="请选择">
                <el-option
                  v-for="item in driverList"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id">
                </el-option>
              </el-select>
            </div>
          </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="6">
            <el-checkbox checked>换盖</el-checkbox>
        </el-col>
        <el-col :span="6">
          <el-form-item label="档口">
            <el-select size="small" v-model="exchangeBottle.store_id"
              filterable
              placeholder="请选择">
              <el-option
                v-for="item in storeList"
                :key="item.store_id"
                :label="item.store_name"
                :value="item.store_id">
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-input placeholder="换盖金额" v-model="exchangeBottle.amount">
            <el-button slot="append" @click="handleExchangeBottle()">保存</el-button>
          </el-input>
        </el-col>
      </el-row>
    </el-row>
  </el-form>
</template>

<script>
import { fetchList, fetchUpdate } from 'api/restfull';
import { showMsg } from 'utils/index';
import OrderGoodsList from './_orderGoodsList.vue';

export default {
  name: 'MainOrderDetail',
  components: { OrderGoodsList },
  props: {
    mainOrder: {
      type: Object,
      default: null
    },
    inputStaus: {
      tyep: String,
      default: 'edit'
    }
  },
  data() {
    return {
      storeList: [{ store_id: 12, store_name: '档口' }],
      driverList: [{ id: 1, name: '司机' }],
      jushou_amount: '',
      shishou_amount: '',
      goodsListVisible: true,
      exchangeBottle: {
        is_checked: false,
        store_id: '',
        amount: 0
      },
      dateOptions: {
        shortcuts: [{
          text: '今天',
          onClick(picker) {
            picker.$emit('pick', new Date());
          }
        }, {
          text: '昨天',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24);
            picker.$emit('pick', date);
          }
        }, {
          text: '大前天',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24 * 2);
            picker.$emit('pick', date);
          }
        }]
      }
    }
  },
  created() {
    this.jushou_amount = this.mainOrder.jushou
    this.shishou_amount = this.mainOrder.shishou
  },
  computed: {
    'mainOrder.shifa'() {
      console.log('computed:');
      return this.mainOrder.pay_amount - this.jushou_amount - this.quehuo_amount;
    },
    'mainOrder.yingshou'() {
      return this.mainOrder.shifa - this.mainOrder.qiandan - this.mainOrder.ziti
       - this.mainOrder.weicha - this.mainOrder.qita;
    },
    'mainOrder.shishou'() {
      return this.mainOrder.pd_amount + this.mainOrder.pos + this.mainOrder.weixin
       + this.mainOrder.alipay + this.mainOrder.yizhifu + this.mainOrder.yizhifu;
    },
    'mainOrder.quehuo'() {
      this.mainOrder.goods_list.forEach(goods => {
        return goods.goods_price * goods.payments.quehuo_number;
      }).sum()
    }
  },
  methods: {
    getStoreList(store_name) {
      fetchList({ store_name }, '/stores').then(response => {
        this.storeList = response.data
      })
    },
    getDriverList(name) {
      fetchList({ name }, '/drivers').then(response => {
        this.driverList = response.data
      })
    },
    handleExchangeBottle() {
      fetchUpdate(this.exchangeBottle, 'exchange_bottles').then(response => {
        showMsg(response.data)
      })
    },
    handleReCalculate() {
      fetchList({}, '/main_order_payments/' + this.mainOrder.pay_id + '/reCaculate').then(response => {
        showMsg(response.data)
      })
    },
    toogleGoodsList() {
      console.log('toogle:' + this.goodsListVisible);
      this.goodsListVisible = !this.goodsListVisible;
    },
    handleGoodsListChange(goodsList) {
      this.mainOrder.goods_list = goodsList;
    }
  }
}
</script>

<style lang="scss">
.el-row {
    margin-bottom: 5px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .el-col {
    margin-bottom: 5px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .form-title {
    text-align: center;
  }
  .form-line-head {
    background: #e5e9f2;
  }
  .form-line-head-desc {
    float: right;
  }
</style>
