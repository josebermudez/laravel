<template>
  <div class="main-content">
    <div class="page-header">
      <h3 class="page-title">{{ $t("products.products") }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{ $t("general.home") }}</a></li>
        <li class="breadcrumb-item"><a href="#">{{ $t("products.products") }}</a></li>
        <li class="breadcrumb-item active">{{ $t("general.all") }}</li>
      </ol>
      <div class="page-actions">
      
		 <button class="btn btn-primary" @click="openCreateModal">
			<i class="icon-fa icon-fa-plus"/> {{ $t("products.actions.new") }}
		 </button>
          
        <button class="btn btn-danger">
          <i class="icon-fa icon-fa-trash"/> {{ $t("products.actions.delete") }}
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h6>{{ $t("products.labels.all") }}</h6>
            <div class="card-actions" />
          </div>
          <div class="card-body">
            <table-component
              :data="getProducts"
              sort-by="created_at"
              sort-order="desc"
              table-class="table"
			  v-bind:filter-no-results="i18n.table.filterNoResults"
			  v-bind:filter-placeholder="i18n.table.filterPlaceholder"
			  ref="productTable"
            >
              <table-column show="name" v-bind:label="i18n.columns.name"/>
              <table-column show="description" v-bind:label="i18n.columns.description"/>
              <table-column show="stock" v-bind:label="i18n.columns.stock"/>
              <table-column
                show="created_at"
                v-bind:label="i18n.columns.registered_on"
                data-type="date:YYYY-MM-DD h:i:s"
              />
              <table-column
                :sortable="false"
                :filterable="false"
                label=""
              >
                <template slot-scope="row">
                  <div class="table__actions">
                    <router-link to="/admin/products/view">
                      <a class="btn btn-default btn-sm">
                        <i class="icon-fa icon-fa-search"/> {{ $t("products.actions.view") }}
                      </a>
                    </router-link>
                    <a
                      class="btn btn-default btn-sm"
                      data-delete
                      data-confirmation="notie"
                      @click="deleteProduct(row)"
                    >
                      <i class="icon-fa icon-fa-trash"/> {{ $t("products.actions.delete") }}
                    </a>
                  </div>
                </template>
              </table-column>
            </table-component>
          </div>
        </div>
      </div>
	  <create-modal ref="create"/>
	  <block-u-i ref="blockUi"/>
	  
    </div>
  </div>
</template>

<script type="text/babel">
import { TableComponent, TableColumn } from 'vue-table-component'
import CreateModal from './products/CreateModal'
import BlockUI from '../../../components/custom/BlockUI'

export default {
  components: {
    TableComponent,
    TableColumn,
	CreateModal,
	BlockUI
  },
  mounted: function() {
	var tableFilter = this.$el.querySelector('table-component__filter__field');
	console.log(tableFilter);
	
  },
  data () {
    return {
	  i18n: {
		  columns: {
			stock: this.$t('products.labels.stock'),
			name: this.$t('products.labels.name'),
			description: this.$t('products.labels.description'),
			registered_on: this.$t('general.labels.registered_on'),
		  },
		table: {
			filterNoResults: this.$t('general.labels.no_rows'),
			filterPlaceholder: this.$t('general.labels.filter_place_holder'),
		}
	  },
	  msg: 'Welcome to Your Vue.js App',
	  sizeUnit: "px",
	  color: "#bada55"
    }
  },
  methods: {
    async getProducts ({ page, filter, sort }) {

		let data
		
		this.$refs.blockUi.loading = true
		let self = this
		
        const response = await axios.get('/api/admin/products/get', { params: {page: page, filter:filter, sort:sort} })
						.then((response) => {
						
							data = {
								data: response.data.data,
								pagination: {
									currentPage: response.data.current_page,
									totalPages: response.data.last_page,
									count: response.data.count
								}
							}	
						}).catch(function (error) {
						  window.toastr['error'](self.$t('general.error'), 'Error')
						  console.log(error)
						})
						.finally(function () {
						  self.$refs.blockUi.loading = false
						})
		return data
    },
    deleteProduct (row) {
      let self = this

      window.notie.confirm({
        text: this.$t('general.labels.confirm', { name: row.name }),
        cancelCallback: function () {
          window.toastr['info']('Cancel')
        },
        submitCallback: function () {
          self.deleteUserData(row.id)
        },
		submitText: this.$t('general.labels.yes'),
		cancelText: this.$t('general.labels.cancel')
      })
    },
    async deleteUserData (id) {
	
	  this.$refs.blockUi.loading = true
	  let self = this
	  
      await window.axios.delete('/api/admin/products/' + id)
			.then(function (response){
			  self.$refs.productTable.refresh()
			  window.toastr['success'](response.data.message, self.$t('general.success'))
			})
			.catch(function (error) {
			  window.toastr['error'](self.$t('general.error'), 'Error')
			  console.log(error)
			})
			.finally(function () {
			  self.$refs.blockUi.loading = false
			});
    },
	openCreateModal () {
		this.$refs.create.openModal()
	}
  }
}
</script>