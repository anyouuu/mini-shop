<template>
	<view :class="Visible ? 'address-distr_open' : 'address-distr_close'">
		<view class="address-list bg-white">
			<scroll-view scroll-y="true" class="specs mt20" style="max-height: 600rpx;">
				<view class="address p30 d-s-c border-b-e" v-for="(item,index) in storeList" :key="index">
					<view class="info flex-1" @click="onSelectedStore(item)">
						<view class="user f34">
							<text>{{item.store_name}}</text>
						</view>
						<view class="pt10 user f30 gray6">
							<text>{{item.phone}}</text>
						</view>
						<view class="pt10 f24 gray6">
							<text> {{item.region.province}}{{item.region.city}}{{item.region.region}}{{item.address}}</text>
						</view>
						<view>
							<text class="iconfont icon-dingwei"></text>
							<text>{{item.distance_unit}}</text>
						</view>
						<!-- 选中状态 -->
						<view v-if="item.store_id == selectedId" class="shop-item__right">
							<text class="iconfont icon-iconfontduihaocopy"></text>
						</view>
					</view>
				</view>
			</scroll-view>	
			<!-- 无数据提供的页面 -->
			<view v-if="!isLoading && !storeList.length">
				<view class="yoshop-notcont">
					<text class="iconfont icon-wushuju"></text>
					<text class="cont">亲，暂无自提门店哦</text>
				</view>
			</view>
		</view>
	</view>
</template>


<script>
	export default {
		data() {
			return {
				/*数据*/
				listData: [],
				isLoading: true, // 是否正在加载中
				storeList: [], // 门店列表,
				longitude: '',
				latitude: '',
				selectedId: -1,
				Visible:false,
				url: '',
			}
		},
		props: ['isAddress','store_id','chooseSotr'],
		watch:{
			isAddress(val){
				this.Visible=val;
				if(val){
					//#ifdef H5
					if(this.isWeixin()){
						this.url = window.location.href;
					}
					//#endif
					// 记录已选中的id
					this.selectedId = this.$props.store_id;
					this.getData(true);
				}
			}
		},
		methods: {
			/*获取数据*/
			getData(isFirst) {
				let self = this;
				self.isLoading = true;
				self._get('store.store/lists', {
					longitude: 0,
					latitude: 0,
					shop_supplier_id:self.$props.chooseSotr,
					url: self.url
				}, function(res) {
					self.isLoading = false;
					self.storeList = res.data.list;
				});
			},
			/* 选择门店 */
			onSelectedStore(e) {
				let self = this;
				self.selectedId = e;
				// 设置上级页面的门店id
				self.$fire.fire('selectStoreId',e);	
				this.$emit('close',e);
			}
		}
	}
</script>

<style>
	.address-list {
		padding-bottom: 90rpx;
	}

	.foot-btns {
		padding: 0;
	}

	.foot-btns .btn-red {
		width: 100%;
		height: 90rpx;
		line-height: 90rpx;
		border-radius: 0;
	}
	.address-distr_open{
		position: absolute;
		bottom: env(safe-area-inset-bottom);
		left: 0;
		width: 100%;
		display: block;
	}
	.address-distr_close{
		position: absolute;
		bottom: env(safe-area-inset-bottom);
		left: 0;
		width: 100%;
		display: none;
	}
</style>
