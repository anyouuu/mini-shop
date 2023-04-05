<template>
	<view>
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" lower-threshold="50"
			@scrolltolower="scrolltolowerFunc">
			<view class="address-list">
				<view class="address pt26 p-0-26 bg-white"   @click="onSelectedStore(item.shop_supplier_id)"
					:class="isShopid(item.shop_supplier_id)?'active':''" v-for="(item,index) in storeList" :key="index">
					<view class="info flex-1">
						<view class="user f34 d-b-c">
							<view class="d-s-c">
								<view 
									:class="item.shop_supplier_id != selectedId?'select-id':'select-id icon iconfont icon-tijiaochenggong'">
								</view>
								<view class="f32 fb gray3">{{item.name}}</view>
							</view>
						</view>
						<view class="d-b-c">
							<view class="text-ellipsis f22 gray6">
								{{item.address}}
							</view>
							<view class="text-ellipsis f22 gray6">
								<text class="icon iconfont icon-time"
									style="color: #333333;font-size: 22rpx;margin-right: 15rpx;"></text>
								<text  v-if="item.delivery_time.length > 0">{{item.delivery_time[0] + '-' + item.delivery_time[1]}}</text>
								<text  v-else>24小时</text>
							</view>
						</view>
						<view class="mt16 mb30 d-b-c">
							<view></view>
							<view>
								<text class="shop_status theme-borderbtn" v-if="item.status==0">营业中</text>
								<text class="shop_status theme-borderbtn" v-else>暂停营业</text>
							</view>

						</view>
					</view>
					<view class="info-right d-b-c">
						<view class="d-s-c gray3">
							<view class="d-s-c" @click.stop="callPhone(item.link_phone)">
								<view class="icon iconfont icon-fasong"></view><text class="f26 fb ml18">咨询服务方</text>
							</view>
							<view class="ml25  d-s-c" @click.stop="openmap(item.latitude,item.longitude)">
								<view class="icon iconfont icon-002dianhua"></view><text class="f26 fb ml18">导航去这里</text>
							</view>
							
						</view>
						<view class="f26 gray3 theme-btn"  @click.stop="onSelectedStore(item.shop_supplier_id,true)">去下单</view>
					</view>
				</view>
				<!-- 没有记录 -->
				<view class="d-c-c p30" v-if="storeList.length==0 && !loading">
					<text class="iconfont icon-wushuju"></text>
					<text class="cont">亲，暂无相关记录哦</text>
				</view>
				<uni-load-more v-else :loadingType="loadingType"></uni-load-more>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import uniLoadMore from "@/components/uni-load-more.vue";
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				/*数据*/
				distance: '',
				loading: true, // 是否正在加载中
				storeList: [], // 门店列表,
				longitude: '',
				latitude: '',
				selectedId: {
					region: {
						city: '',
						store_id: -1,
						distance_unit: '',
						log: {
							file_path: ''
						}
					},
					store_name: ''
				},
				/* 搜索关键字 */
				store_name: '',
				list_rows: 10,
				last_page: 0,
				page: 1,
				/*没有更多*/
				no_more: false,
				scrollviewHigh: 0,
				longitude: '',
				latitude: '',
				selectedId: ''
			}
		},
		computed: {
			/*加载中状态*/
			loadingType() {
				if (this.loading) {
					return 1;
				} else {
					if (this.storeList.length != 0 && this.no_more) {
						return 2;
					} else {
						return 0;
					}
				}
			}
		},
		onLoad(options) {},
		onShow() {
			
		},
		mounted() {
			this.selectedId = uni.getStorageSync('selectedId');
			this.restoreData();
			this.getData();
			this.init();
		},
		methods: {
			/*初始化*/
			init() {
				let _this = this;
				uni.getSystemInfo({
					success(res) {
						_this.scrollviewHigh = res.windowHeight;
					}
				});
			},
			/*还原初始化*/
			restoreData() {
				this.storeList = [];
				this.search = '';
				this.no_more = false;
				this.page = 1;
			},
			/*可滚动视图区域到底触发*/
			scrolltolowerFunc() {
				let self = this;
				self.bottomRefresh = true;
				self.page++;
				self.loading = true;
				if (self.page > self.last_page) {
					self.loading = false;
					self.no_more = true;
					return;
				}
				self.getData();
			},
			/*获取数据*/
			getData() {
				let self = this;
				self.loading = true;
				uni.showLoading({
					title: '加载中'
				})
				self._get('supplier.index/list', {
					longitude:  0,
					latitude:  0
				}, function(res) {
					uni.hideLoading()
					self.loading = false;
					self.storeList = self.storeList.concat(res.data.list.data);
					self.last_page = res.data.list.last_page;
					if (res.data.list.last_page <= 1) {
						self.no_more = true;
					}
				});
			},
			search() {
				let self = this;
				self.loading = true;
				self._get('store.store/lists', {
					store_name: self.store_name,
					longitude: 0,
					latitude: 0
				}, function(res) {
					self.loading = false;
					self.storeList = res.data.list;
				});
			},
			/**
			 * 选择门店
			 */
			onSelectedStore(e,flag) {
				let self = this;
				self.selectedId = e;
				uni.setStorageSync('selectedId', self.selectedId);
				if(flag){
					// #ifndef H5
					uni.navigateBack();
					// #endif
					// #ifdef H5
					history.go(-1);
					// #endif
				}
			},
			addSotre(e) {
				let self = this;
				let store_id = e.store_id;
				self._post(
					'user.storelog/add', {
						store_id: store_id
					},
					function(res) {

					}
				);
			},
			/* 是否选择 */
			isShopid(id) {
				let s_id = uni.getStorageSync('selectedId');
				return s_id == id
			}
		}
	}
</script>

<style lang="scss">
	.info {
		margin-left: 20rpx;
	}

	.address-list {
		padding: 30rpx 0;
	}

	.address-list .address {
		margin-bottom: 22rpx;
	}

	.address-list .address .info-right {
		height: 100rpx;
		border-top: 2rpx solid #EEEEEE;
		flex-shrink: 0;
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

	.address_img image {
		width: 154rpx;
		height: 154rpx;
		border-radius: 15rpx;
	}

	.address_tit {
		height: 75rpx;
		line-height: 75rpx;
		color: #959597;
		font-size: 29rpx;
		padding-left: 28rpx;
	}

	.address_btn {
		width: 130rpx;
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
		text-align: center;
		background-color: $dominant-color;
		margin-top: 50rpx;
		color: #FFFFFF;
	}

	.user {
		font-size: 30rpx;
		font-family: PingFang SC;
		font-weight: 400;
		color: #333333;
		opacity: 1;
		margin: 10rpx 0;
	}

	.distance {
		font-size: 22rpx;
		color: #666666;
	}

	.address_search {
		background-color: #f6f6f6;
		width: 460rpx;
		height: 65rpx;
		border-radius: 33rpx;
		margin-left: 29rpx;
		margin-right: 30rpx;
	}

	.address_city {
		display: flex;
		align-items: center;
		margin-left: 33rpx;
	}

	.icon-sousuo {
		margin-left: 22rpx;
		margin-right: 9rpx;
	}

	.icon-jiantoushang {
		font-size: 17rpx;
		display: block;
		transform: rotate(180deg);
		color: #000000;
		margin-left: 22rpx;
	}

	.ml25 {
		margin-left: 25rpx;
	}

	.shop_status {
		padding: 4rpx 8rpx;
		border-radius: 5rpx;
	}

	.shop_status text {
		// color: $dominant-color;
		// border: 1rpx solid $dominant-color;
		padding: 3rpx 8rpx;
		border-radius: 4rpx;
		margin-left: 9rpx;
	}

	.select-id {
		width: 38rpx;
		height: 38rpx;
		border-radius: 50%;
		border: 1rpx solid #D9D9D9;
		box-sizing: border-box;
		margin-right: 24rpx;
	}

	.select-id.icon-tijiaochenggong {
		color: #FFFFFF;
		display: flex;
		justify-content: center;
		align-items: center;
		font-size: 22rpx;
		border-color: $dominant-color;
		background-color: $dominant-color;
	}

	.info-right .theme-btn {
		width: 142rpx;
		border-radius: 30rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		height: 60rpx;
		font-size: 28rpx;
		font-weight: 800;
		color: #FFFFFF;
	}
	.info-right .icon-fasong{
		width: 51rpx;
		height: 51rpx;
		border-radius: 50%;
		display: flex;
		justify-content: center;
		align-items: center;
		background: #00CEC9;
		color: #FFFFFF;
		box-shadow: 0px 5rpx 5rpx 0px rgba(0, 206, 201, 0.09);
	}
	.info-right  .icon-002dianhua{
		color: #FFFFFF;
		width: 51rpx;
		height: 51rpx;
		background: #FFCC00;
		display: flex;
		justify-content: center;
		align-items: center;
		box-shadow: 0px 5rpx 5rpx 0px rgba(255, 204, 0, 0.09);
		border-radius: 50%;
	}
</style>
