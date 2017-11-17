<template>
  <el-table
      ref='orderGoods'
      :data="orderGoodsList"
      max-height="250"
      style="width: 100%">
      <el-table-column width="100px" align="center" label="SKU">
          <template slot-scope="scope">
              <span>{{scope.row.goods_id}}</span>
          </template>
      </el-table-column>
      <el-table-column align="center" label="货品名称">
        <template slot-scope="scope">
          <span>{{scope.row.goods_name}}</span>
        </template>
      </el-table-column>
      <el-table-column width="110px" align="center" label="条码">
        <template slot-scope="scope">
          <span>{{scope.row.goods_serial}}</span>
        </template>
      </el-table-column>
      <el-table-column width="150px" align="center" label="档口名称">
        <template slot-scope="scope">
          <span>{{scope.row.store_id}}</span>
        </template>
      </el-table-column>
      <el-table-column width="150px" align="center" label="单价">
        <template slot-scope="scope">
          <span>{{scope.row.goods_price}}</span>
        </template>
      </el-table-column>
      <el-table-column width="110px" align="center" label="数量">
        <template slot-scope="scope">
          <span>{{scope.row.goods_num}}</span>
        </template>
      </el-table-column>
      <el-table-column width="110px" align="center" label="缺货数量">
        <template slot-scope="scope">
          <el-input size="small" v-model.number="scope.row.payments.quehuo_number"></el-input>
        </template>
      </el-table-column>
      <el-table-column width="110px" fix="right" align="center" label="拒收数量">
        <template slot-scope="scope">
          <el-input size="small" v-model.number="scope.row.payments.jushou_number"></el-input>
        </template>
      </el-table-column>
      <el-table-column width="110px" fix="right" align="center" label="实发数量">
        <template slot-scope="scope">
          <span>{{scope.row.payments.shifa_number}}</span>
        </template>
      </el-table-column>
  </el-table>
</template>

<script>

export default {
  name: 'OrderGoodsListTable',
  props: {
    goodsList: {
      type: Array,
      default: null
    }
  },
  data() {
    return {
    }
  },
  computed: {
    orderGoodsList() {
      return this.goodsList;
    }
  },
  watch: {
    orderGoodsList: {
      handler(list) {
        list.map(item => {
          // @TODO 初始化 payments
          if (_.isNull(item.payments)) {
            item.payments = {
              goods_id: item.goods_id,
              quehuo_number: 0,
              jushou_number: 0
            }
          }
          item.payments.shifa_number = item.goods_num - item.payments.quehuo_number - item.payments.jushou_number
          return item;
        });
        this.$emit('goods-change', list)
      },
      immediate: true,
      deep: true
    }
  }
}
</script>

<style lang="css">
</style>
