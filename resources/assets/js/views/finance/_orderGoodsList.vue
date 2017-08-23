<template>
  <el-table
      ref='orderGoods'
      :data="orderGoodsList"
      style="width: 100%">
      <el-table-column width="210px" align="center" label="货品名称">
        <template scope="scope">
          <span>{{scope.row.goods_name}}</span>
        </template>
      </el-table-column>
      <el-table-column width="110px" align="center" label="条码">
        <template scope="scope">
          <span>{{scope.row.goods_serial}}</span>
        </template>
      </el-table-column>
      <el-table-column width="150px" align="center" label="档口名称">
        <template scope="scope">
          <span>{{scope.row.store_id}}</span>
        </template>
      </el-table-column>
      <el-table-column width="150px" align="center" label="单价">
        <template scope="scope">
          <span>{{scope.row.goods_price}}</span>
        </template>
      </el-table-column>
      <el-table-column width="110px" align="center" label="数量">
        <template scope="scope">
          <span>{{scope.row.goods_num}}</span>
        </template>
      </el-table-column>
      <el-table-column width="110px" align="center" label="缺货数量">
        <template scope="scope">
          <el-input v-show="scope.row.edit" size="small" v-model="scope.row.payments.quehuo_number"></el-input>
          <span v-show="!scope.row.edit">{{ scope.row.payments.quehuo_number }}</span>
        </template>
      </el-table-column>
      <el-table-column width="110px" fix="right" align="center" label="拒收数量">
        <template scope="scope">
          <el-input v-show="scope.row.edit" size="small" v-model="scope.row.payments.jushou_number"></el-input>
          <span v-show="!scope.row.edit">{{ scope.row.payments.jushou_number }}</span>
        </template>
      </el-table-column>
      <el-table-column width="110px" fix="right" align="center" label="实发数量">
        <template scope="scope">
          <span>{{scope.row.payments.shifa_number}}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" fix="right" label="编辑" width="120">
        <template scope="scope">
          <el-button v-show='!scope.row.edit' type="primary" @click="handleEdit(scope.$index, orderGoodsList)" size="small" icon="edit">编辑</el-button>
          <el-button v-show='scope.row.edit' type="success" @click="handleSave(scope.$index, orderGoodsList)" size="small" icon="check">完成</el-button>
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
          item.payments.shifa_number = item.goods_num - item.payments.quehuo_number - item.payments.jushou_number
          return item;
        });
        this.$emit('goods-change', list)
      },
      deep: true
    }
  },
  methods: {
    handleEdit(index, rows) {
      console.log('handleEdit:');
      rows[index].edit = true;
    },
    handleSave(index, rows) {
      console.log('handleSave:');
      console.log(rows[index]);
      rows[index].edit = false;
    }
  }
}
</script>

<style lang="css">
</style>
