<template>
  <el-select v-model="selectAt" filterable
    placeholder="请输入关键词">
    <el-option
      v-for="item in selectOptions"
      :key="item.id"
      :label="item.name"
      :value="item.id">
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
      fetchList({}, '/drivers_list').then(response => {
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
