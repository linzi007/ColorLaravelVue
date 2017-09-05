<template>
  <el-select v-model="selectAt" filterable
    placeholder="请输入关键词">
    <el-option label="全部" value=""></el-option>
    <el-option
      v-for="item in selectOptions"
      :key="item.store_id"
      :label="item.store_name"
      :value="item.store_id">
    </el-option>
  </el-select>
</template>
<script>
import { fetchList } from 'api/restfull';
export default {
  props: {
    selected: {
      default: ''
    }
  },
  data() {
    return {
      selectOptions: [],
      selectAt: '',
      loading: false
    }
  },
  mounted() {
    this.selectAt = this.selected;
    this.getOptions();
  },
  methods: {
    getOptions() {
      fetchList({}, '/stores').then(response => {
        this.selectOptions = response.data;
      })
    }
  },
  watch: {
    selectAt(v) {
      this.$emit('changeSelect', v)
    }
  }
}
</script>

<style lang="css">
</style>
