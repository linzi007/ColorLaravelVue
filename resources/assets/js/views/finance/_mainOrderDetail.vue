<template>
  <el-form :model="orderPayments" ref="orderMainForm">
    <el-row>
        <h3 class="form-title">订单信息</h3>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="支付单号">
            <span class="form-item">
              {{orderPayments.pay_sn}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="订单时间">
            <span class="form-item">
              {{orderPayments.add_time}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="门店名称">
            <span class="form-item">
              {{orderPayments.receiver_shop_name}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="收货人">
            <span class="form-item">
              {{orderPayments.receiver_name}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="16">
          <el-form-item label="详细地址">
            <span class="form-item">
              {{orderPayments.receiver_area_info}} {{orderPayments.receiver_address_detail}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>
    <el-row>
      <span class="form-line-head">实发金额{{orderPayments.shifa}}</span><span class="form-line-head form-line-head-desc">（备注实发=货品金额-缺货-拒收）</span>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="货品金额">
            <span class="form-item">
              {{orderPayments.receiver_name}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <el-form-item label="缺货金额">
            <span class="form-item">
              {{orderPayments.quehuo}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <el-form-item label="拒收金额">
            <span class="form-item">
              {{orderPayments.jushou}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="4">
            <el-button @click="toogleGoodsList()">缺货/拒收录入</el-button>
        </el-col>
    </el-row>
    <el-row>
      <order-goods-list :goodsList="orderPayments.goods_list" @goods-change="handleGoodsListChange"
        v-if="goodsListVisible">
      </order-goods-list>
    </el-row>

    <el-row>
      <span class="form-line-head">应收金额{{yingshouAmount}}</span><span class="form-line-head form-line-head-desc">（备注：应收=实发金额-签单-自提-其他-尾差）</span>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="签单金额：">
            <el-input placeholder="签单金额" v-model="orderPayments.qiandan">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="自提金额：">
            <el-input placeholder="自提金额" v-model="orderPayments.ziti">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="尾差金额：">
            <el-input placeholder="尾差金额" v-model="orderPayments.weicha">
            </el-input>
          </el-form-item>
        </el-col>

        <el-col :span="8">
          <el-form-item label="其他金额：">
            <el-input placeholder="其他金额" v-model="orderPayments.qita">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="备注：">
            <el-input type="textarea" :autosize="{ minRows: 1, maxRows: 4}"
              placeholder="扣减说明" v-model="orderPayments.desc_remark">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="代金券：">
            <span class="form-item">
              {{orderPayments.promotion_amount}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>

    <el-row>
      <span  class="form-line-head">实收金额{{shishouAmount}}</span><span class="form-line-head form-line-head-desc">（备注：实收=预存款+POS+微信+支付宝+现金）</span>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="8">
        <el-form-item label="预存款金额：">
          <span class="form-item">
            {{orderPayments.pd_amount}}
          </span>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="POS金额：">
          <el-input placeholder="POS金额" v-model="orderPayments.pos">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="刷卡单号：">
          <el-input placeholder="刷卡单号" v-model="orderPayments.out_pay_sn">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="微信金额：">
          <el-input placeholder="微信金额" v-model="orderPayments.weixin">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="支付宝金额：">
          <el-input placeholder="支付宝金额" v-model="orderPayments.alipay">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="现金：">
          <el-input placeholder="现金" v-model="orderPayments.cash">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="翼支付金额：">
          <el-input placeholder="翼支付金额" v-model="orderPayments.yizhifu">
          </el-input>
        </el-form-item>
      </el-col>

      <el-col :span="8">
        <el-form-item label="收款日期：">
          <el-date-picker
            v-model="orderPayments.jk_at"
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
            v-model="orderPayments.ck_at"
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
            v-model="orderPayments.remark">
          </el-input>
        </el-form-item>
      </el-col>
    </el-row>
    <el-row>
      <span class="form-line-head">配送费</span>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="12">
        <el-form-item label="货物配送费">
          <el-input placeholder="货物配送费" v-model="orderPayments.shipping_fee">
            <el-button slot="append" @click="handleReCalculate()">重算</el-button>
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="司机配送费">
          <el-input placeholder="司机配送费" v-model="orderPayments.driver_fee">
            <el-button slot="append" @click="handleReCalculate()">重算</el-button>
          </el-input>
        </el-form-item>
      </el-col>
      <el-row>
          <el-col :span="6">
              <el-checkbox v-model="orderPayments.sencod_driver_id" :checked="orderPayments.sencod_driver_id">二次配送</el-checkbox>
          </el-col>
          <el-col :span="18">
            <div>
              <span>首次配送司机</span>
              <el-select
                v-model="orderPayments.driver_id"
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
  computed: {
    orderPayments() {
      return this.mainOrder;
    },
    shifaAmount() {
      return this.getShifaAmount();
    },
    yingshouAmount() {
      return this.getYingshouAmount()
    },
    shishouAmount() {
      return this.getShishouAmount();
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
      fetchList({}, '/main_order_payments/' + this.orderPayments.pay_id + '/reCaculate').then(response => {
        showMsg(response.data)
      })
    },
    toogleGoodsList() {
      console.log('toogle:' + this.goodsListVisible);
      this.goodsListVisible = !this.goodsListVisible;
    },
    handleGoodsListChange(change) {
      this.orderPayments.goods_list = change;
      let sum_jushou = 0.0;
      let sum_quehuo = 0.0;
      _.forEach(this.orderPayments.goods_list, (n, key) => {
        console.log(key);
        console.log(n.payments);
        sum_jushou = _.add(n.payments.jushou_number * n.goods_price, sum_jushou)
        sum_quehuo = _.add(n.payments.quehuo_number * n.goods_price, sum_quehuo)
      });
      console.log('watch change:');
      console.log('jushou sum:');
      console.log(sum_jushou);
      console.log('quehuo sum:');

      console.log(sum_quehuo);

      this.orderPayments.jushou = sum_jushou
      this.orderPayments.quehuo = sum_quehuo
      this.orderPayments.shifa = this.getShifaAmount()
    },
    handleNull() {
      if (_.isNull(this.orderPayments.pd_amount)) {
        this.orderPayments.pd_amount = 0;
      }
      if (_.isNull(this.orderPayments.pos)) {
        this.orderPayments.pos = 0;
      }
      if (_.isNull(this.orderPayments.weixin)) {
        this.orderPayments.weixin = 0;
      }
      if (_.isNull(this.orderPayments.alipay)) {
        this.orderPayments.alipay = 0;
      }
      if (_.isNull(this.orderPayments.yizhifu)) {
        this.orderPayments.yizhifu = 0;
      }
      if (_.isNull(this.orderPayments.cash)) {
        this.orderPayments.cash = 0;
      }
    },
    getShishouAmount() {
      this.handleNull();
      return _.add(_.add(_.add(_.add(_.add(parseFloat(this.orderPayments.pd_amount), parseFloat(this.orderPayments.pos)), parseFloat(this.orderPayments.weixin)), parseFloat(this.orderPayments.alipay)), parseFloat(this.orderPayments.yizhifu)), parseFloat(this.orderPayments.cash))
    },
    getYingshouAmount() {
      return this.orderPayments.shifa - this.orderPayments.qiandan - this.orderPayments.ziti
       - this.orderPayments.weicha - this.orderPayments.qita
    },
    getShifaAmount() {
      return this.orderPayments.order_amount - this.orderPayments.jushou - this.orderPayments.quehuo
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
