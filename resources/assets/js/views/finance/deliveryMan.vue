<template>
  <div class="app-container calendar-list-container">
    <div class="filter-container">
      <el-input placeholder="司机姓名" style="width: 100px" v-model="listQuery.name">
      </el-input>
      <el-input placeholder="电话" style="width: 100px" v-model="listQuery.mobile">
      </el-input>

      <el-button class="filter-item" type="primary" icon="search"
        @click="handleFilter">搜索</el-button>
      <el-button class="filter-item" style="margin-left: 10px;"
        @click="handleCreate" type="primary" icon="edit">添加</el-button>
      <el-button class="filter-item" type="primary" icon="document"
        @click="handleDownload">导出</el-button>
    </div>
    <el-table
        :data="list" :key='tableKey' v-loading.body="listLoading"
        boder fit highlight-current-row
        @row-dblclick='handleUpdate'
        style="width: 100%">
      <el-table-column align="center" label="序号" width="65">
        <template scope="scope">
          <span>{{scope.row.id}}</span>
        </template>
      </el-table-column>
      <el-table-column
          prop="code"
          label="编码">
      </el-table-column>
      <el-table-column
          prop="name"
          label="司机名称">
      </el-table-column>
      <el-table-column
          prop="description"
          label="备注">
      </el-table-column>
    </el-table>

    <div v-show="!listLoading" class="pagination-container">
      <el-pagination @size-change="handleSizeChange"
      @current-change="handleCurrentChange" :current-page.sync="listQuery.current_page"
        :page-sizes="[10,20,30, 50]" :page-size="listQuery.per_page"
        layout="total, sizes, prev, pager, next, jumper" :total="total">
      </el-pagination>
    </div>

    <el-dialog :title="dialogTextMap[dialogStatus]"
    :visible.sync="dialogFormVisible">
      <el-form class="small-space" :model="temp" label-position="left" label-width="70px" style='width: 400px; margin-left:50px;'>
        <el-form-item label="编号">
          <el-input v-model="temp.code"></el-input>
        </el-form-item>
        <el-form-item label="司机名称">
          <el-input v-model="temp.name"></el-input>
        </el-form-item>
        <el-form-item label="手机">
          <el-input v-model="temp.mobile"></el-input>
        </el-form-item>
        <el-form-item label="备注">
          <el-input v-model="temp.description"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button v-if="dialogStatus=='create'" type="primary" @click="create">确 定</el-button>
        <el-button v-else type="primary" @click="update">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
  import { fetchList, fetchCreate, fetchUpdate, fetchDelete } from 'api/restfull';

  export default {
    name: 'drivers',
    data() {
      return {
        listLoading: true,
        list: null,
        total: null,
        pagePath: '/drivers',
        listQuery: {
          current_page: 1,
          per_page: 20,
          mobile: undefined,
          name: undefined
        },
        dialogFormVisible: false,
        dialogStatus: '',
        dialogTextMap: {
          update: '编辑',
          create: '创建'
        },
        tableKey: 0,
        temp: {}
      }
    },
    created() {
      this.resetTemp();
      this.getList();
    },
    methods: {
      getList() {
        this.listLoading = true;
        fetchList(this.listQuery, '/drivers').then(response => {
          this.list = response.data.data;
          this.total = response.data.total;
          this.pagePath = response.data.path;
          this.listLoading = false;
        })
      }, // 搜索
      handleFilter() {
        this.getList();
      }, // 新建
      handleCreate() {
        this.resetTemp();
        this.dialogStatus = 'create';
        this.dialogFormVisible = true;
      },
      handleUpdate(row) {
        this.temp = Object.assign({}, row);
        this.dialogStatus = 'update';
        this.dialogFormVisible = true;
      },
      handleDelete(row) {
        fetchDelete('/drivers').then(response => {
          if (response.status !== 200) {
            this.$message({
              message: response.data.message,
              type: 'error'
            });
            return false;
          }
        })
        this.$notify({
          title: '成功',
          message: '删除成功',
          type: 'success',
          duration: 2000
        });
        const index = this.list.indexOf(row);
        this.list.splice(index, 1);
      },
      create() {
        fetchCreate(this.temp, '/drivers').then(response => {
          if (response.status !== 200) {
            this.$message({
              message: response.data.message,
              type: 'error'
            });
            this.dialogFormVisible = false;
            return false;
          }
          this.temp.id = response.data.id;
          this.temp.code = response.data.code;
          this.temp.name = response.data.name;
          this.temp.description = response.data.description;
          this.list.unshift(this.temp);
        })
        this.dialogFormVisible = false;
        this.$notify({
          title: '成功',
          message: '创建成功',
          type: 'success',
          duration: 2000
        });
      },
      update() {
        fetchUpdate(this.temp, '/drivers/' + this.temp.id).then(response => {
          if (response.status !== 200) {
            this.$message({
              message: response.data.message,
              type: 'error'
            });
            this.dialogFormVisible = false;
            return false;
          }
        })
        for (const v of this.list) {
          if (v.id === this.temp.id) {
            const index = this.list.indexOf(v);
            this.list.splice(index, 1, this.temp);
            break;
          }
        }
        this.dialogFormVisible = false;
        this.$notify({
          title: '成功',
          message: '更新成功',
          type: 'success',
          duration: 2000
        });
      },
      resetTemp() {
        this.temp = {
          id: undefined,
          code: '',
          name: '',
          mobile: '',
          description: undefined
        };
      },
      handleSizeChange(val) {
        this.listQuery.per_page = val;
        this.getList();
      },
      handleCurrentChange(val) {
        this.listQuery.current_page = val;
        this.getList();
      },
      handleDownload() {
        require.ensure([], () => {
          const { export_json_to_excel } = require('vendor/Export2Excel');
          const tHeader = ['编码', '司机名字', '手机', '备注'];
          const filterVal = ['code', 'name', 'mobile', 'title', 'importance'];
          const data = this.formatJson(filterVal, this.list);
          export_json_to_excel(tHeader, data, 'table数据');
        })
      },
      formatJson(filterVal, jsonData) {
        return jsonData.map(v => filterVal.map(j => {
          if (j === 'timestamp') {
            return parseTime(v[j])
          } else {
            return v[j]
          }
        }))
      }
    }
  }
</script>
