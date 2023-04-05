<template>
	<view class="container" v-if="!loading">
		<view class="main" v-if="goods_list.length">
			<view class="nav">
				<view class="header">
					<view class="left">
						<view class="store-name">
							<text class="fb">{{supplier.name}}</text>
						</view>
					</view>
				</view>
			</view>
			<view class="content">
				<scroll-view class="menus" :style="'height:' + scrollviewHigh + 'px;'"
					:scroll-into-view="menuScrollIntoView" :scroll-with-animation="true" :scroll-animation-duration="1"
					scroll-y>
					<view class="wrapper">
						<view class="menu" :id="`menu-${item.category_id}`"
							:class="{'current': item.category_id === currentCateId}" v-for="(item, index) in goods_list"
							v-if="item.products.length!=0" :key="index" @tap="handleMenuTap(item.category_id)">
							<text>{{ item.name }}</text>
							<view class="dot" v-if="menuCartNum(item.category_id)">{{ menuCartNum(item.category_id) }}
							</view>
						</view>
					</view>
				</scroll-view>
				<!-- goods list begin -->
				<scroll-view class="goods pr" :style="'height:' + scrollviewHigh + 'px;'" :scroll-with-animation="true"
					:scroll-animation-duration="1" scroll-y :scroll-top="cateScrollTop" @scroll="handleGoodsScroll">
					<view class="wrapper">
						<view class="list">
							<!-- category begin -->
							<view class="category" v-for="(item, index) in goods_list" :key="index"
								v-if="item.products.length!=0" :id="`cate-${item.category_id}`">
								<view class="title">
									<text>{{ item.name }}</text>
								</view>
								<view class="items">
									<!-- 商品 begin -->
									<view class="good" v-for="(good, key) in item.products" :key="key">
										<image :src="good.product_image" class="image"></image>
										<view class="right">
											<text class="name">{{ good.product_name }}</text>
											<text class="tips text-ellipsis">{{ good.selling_point }}</text>
											<view class="price_and_action">
												<text class="price">￥{{ good.product_price }}</text>
												<view class="btn-group" v-if="good.spec_types==20">
													<button type="primary" class="btn property_btn" hover-class="none"
														size="min" @tap="getProduct(good)">
														选规格
													</button>
													<view class="dot" v-if="good.cart_num!=0">{{good.cart_num}}</view>
												</view>
												<view class="btn-group" v-else>
													<button v-if="good.cart_num!=0" plain class="btn reduce_btn"
														size="min" hover-class="none" @tap="reduceFunc(good)">
														<view class="iconfont  icon icon-jian iconsami-select"></view>
													</button>
													<view class="number" v-if="good.cart_num!=0">{{ good.cart_num }}
													</view>
													<button class="btn add_btn" size="min" hover-class="none"
														@tap="addCart(good)">
														<view class="iconfont  icon icon-jia iconadd-select"></view>
													</button>
												</view>
											</view>
										</view>
									</view>
									<!-- 商品 end -->
								</view>
							</view>
							<!-- category end -->
						</view>
					</view>
				</scroll-view>
				<!-- goods list end -->
			</view>
			<!-- content end -->
			<!-- 购物车栏 begin -->
			<view class="cart-box" v-if="cart_total_num > 0">
				<view class="mark">
					<image src="/static/images/menu/cart.png" class="cart-img" @tap="openCartPopup"></image>
					<view class="tag">{{ cart_total_num }}</view>
				</view>
				<view class="price"><text class="f-w2">￥</text>{{ total_price }}</view>
				<button type="primary" class="pay-btn" @tap="toPay">
					去结算
				</button>
			</view>
			<!-- 购物车栏 end -->
		</view>
		<prospec :productModel="productModel" :goodDetailModalVisible="goodDetailModalVisible" :orderType="orderType"
			:bag_type="bag_type" @closeGoodDetailModal="closeGoodDetailModal" :dinner_type='dinner_type'
			:cart_type="cart_type"></prospec>
		<!-- 购物车详情popup -->
		<popup-layer direction="top" :show-pop="cartPopupVisible" class="cart-popup">
			<template slot="content">
				<view class="cart-popup">
					<view class="top d-b-c">
						<view>已选商品</view>
						<view @tap="handleCartClear">清空购物车</view>
					</view>
					<scroll-view class="cart-list" scroll-y>
						<view class="wrapper">
							<view class="item" v-if="item.product_num >0" v-for="(item, index) in cart_list"
								:key="index">
								<view class="d-s-s">
									<view class="cart-image">
										<image style="" :src="item.image.file_path" mode=""></image>
									</view>
									<view class="left">
										<view>
											<view class="name">{{ item.product.product_name }}</view>
											<view class="gray9">{{item.describe}}</view>
										</view>
										<view class="center">
											<text class="fb">￥{{ item.price }}</text>
											<text class="f24 gray9" v-if="bag_type!=1">(包装费：￥{{item.bag_price}})</text>
										</view>
									</view>

								</view>
								<view class="right">
									<button plain size="min" class="btn theme-borderbtn" hover-class="none"
										@tap="cartReduce(item)">
										<view class="iconfont icon icon-jian iconsami-select"></view>
									</button>
									<view class="number">{{ item.product_num }}</view>
									<button class="btn theme-btn" size="min" hover-class="none" @tap="cartAdd(item)">
										<view class="iconfont icon icon-jia iconadd-select"></view>
									</button>
								</view>
							</view>
						</view>
					</scroll-view>
				</view>
			</template>
		</popup-layer>
		<!-- 购物车详情popup -->
	</view>
	<view class="loading" v-else>
		<image src="/static/images/loading.gif"></image>
	</view>
</template>

<script>
	import utils from '@/common/utils.js';
	import prospec from './popup/spec.vue';
	import modal from '@/components/modal/modal'
	import popupLayer from '@/components/popup-layer/popup-layer'

	export default {
		components: {
			modal,
			popupLayer,
			prospec
		},
		data() {
			return {
				isLoading: true,
				goods: [], //所有商品
				supplier: {
					name: ''
				},
				ads: [],
				loading: true,
				currentCateId: 6905, //默认分类
				cateScrollTop: 0,
				menuScrollIntoView: '',
				cart: [], //购物车
				goodDetailModalVisible: false, //是否饮品详情模态框
				good: {}, //当前饮品
				category: {}, //当前饮品所在分类
				cartPopupVisible: false,
				sizeCalcState: false,
				listData: [],
				goods_list: [],
				productModel: {},
				clock: false,
				cart_total_num: 0,
				total_price: 0,
				cart_list: [],
				orderType: 'takein',
				takeout_address: {},
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				delivery_time: ['00:00', '00:00'],
				store_time: ['00:00', '00:00'],
				officeTime: {
					now: 0,
					delivery_start: 0,
					delivery_end: 0,
					store_start: 0,
					store_end: 0
				},
				addclock: false,
				longitude: 0,
				latitude: 0,
				bag_type: 1,
				shop_supplier_id: 0,
				/* 10配送20自提30店内 */
				dinner_type: 30,
				cart_type: 1,
				table_id: 0
			}
		},
		onLoad(e) {
			let self = this;
			let scene = utils.getSceneData(e);
			self.table_id = e.table_id;
			this.shop_supplier_id = uni.getStorageSync('selectedId') || 0;
			this.table_id = e.table_id ? e.table_id : scene.table_id;
			if (e.shop_supplier_id || scene.shop_supplier_id) {
				this.shop_supplier_id = e.shop_supplier_id ? e.shop_supplier_id : scene.shop_supplier_id;
			}
		},
		onShow() {
			let self = this;
			self.init();
		},
		computed: {
			menuCartNum() {
				return (id) => this.cart.reduce((acc, cur) => {
					if (cur.cate_id === id) {
						return acc += cur.number
					}
					return acc
				}, 0)
			},
		},
		methods: {
			scrollInit() {
				let self = this;
				uni.getSystemInfo({
					success(res) {
						self.phoneHeight = res.windowHeight;
						// 计算组件的高度
						let view = uni.createSelectorQuery().select('.nav');
						view.boundingClientRect(data => {
							let h = self.phoneHeight - data.height;
							self.scrollviewHigh = h;
						}).exec();
					}
				});
			},
			init() { //页面初始化
				this.category = {};
				this.good = {};
				this.goodDetailModalVisible = false;
				this.clock = false;
				this.getCategory();
				this.sizeCalcState = false;
			},
			/* 获取商品类型 */
			getCategory() {
				let self = this;
				self.loading = true;
				self.isLoading = true;
				uni.showLoading({
					title: '加载中'
				})
				self._get('product.category/index', {
						type: 1,
						/* 0外卖，1店内 */
						shop_supplier_id: self.shop_supplier_id,
						longitude: 0,
						latitude: 0,
						delivery: 40
					}, function(res) {
						self.goods_list = res.data.list;
						self.supplier = res.data.supplier;
						self.shop_supplier_id = res.data.supplier.shop_supplier_id;
						self.bag_type = res.data.supplier.bag_type;
						self.loading = false
						self.isLoading = false;
						self.$nextTick(function() {
							self.scrollInit();
						})
						if (self.getUserId()) {
							self.getCart();
						}
						uni.hideLoading()
					},
					function() {

					}
				);
			},
			/*获取数据*/
			getProduct(item) {
				let self = this;
				if (self.clock == true) {
					return
				}
				self.clock = true;
				self.good = item;
				let product_id = item.product_id;
				self.detail = item;
				self.showGoodDetailModal()
			},
			addCart(goods) {
				let self = this;
				if (self.addclock) {
					return
				}
				if (goods.product_stock <= goods.cart_num) {
					uni.showToast({
						icon: 'none',
						title: "超过限购数量"
					})
					return;
				}
				if (goods.product_stock <= 0 || goods.product_stock <= goods.cart_num) {
					uni.showToast({
						icon: 'none',
						title: '没有更多库存了'
					})
					return;
				}

				let params = {
					product_id: goods.product_id,
					product_num: 1,
					product_sku_id: goods.sku[0].product_sku_id,
					attr: '',
					feed: '',
					describe: '',
					price: goods.sku[0].product_price,
					bag_price: goods.sku[0].bag_price,
					shop_supplier_id: goods.supplier.shop_supplier_id,
					cart_type: 1,
					dinner_type: self.dinner_type
				}
				self.addclock = true;
				self._post('order.cart/add', params, function(res) {
					let num = 1;
					self.cart_total_num++;
					let price = self.total_price * 1 + goods.product_price * 1;
					if (self.bag_type != 1) {
						price += goods.sku[0].bag_price * 1;
					}
					self.total_price = price.toFixed(2)
					if (goods.cart_num) {
						num = goods.cart_num + 1
					}
					self.goods_list.forEach((item, index) => {
						item.products.forEach((product, product_index) => {
							if (product.product_id == goods.product_id) {
								self.$set(product, 'cart_num', product.cart_num + 1)
							}
						});
					})
					self.addclock = false;
				}, function(err) {
					self.addclock = false;
				});
			},
			reduceFunc(goods) {
				let self = this;
				if (self.addclock) {
					return
				}
				let product_id = goods.product_id;
				let num = goods.cart_num;
				self.addclock = true;
				self._post(
					'order.cart/productSub', {
						product_id: product_id,
						type: 'down',
						cart_type: 1,
						dinner_type: self.dinner_type
					},
					function(res) {
						num--;
						self.cart_total_num--;
						let price = self.total_price * 1 - goods.product_price * 1;
						if (self.bag_type != 1) {
							price -= goods.sku[0].bag_price * 1;
						}
						self.total_price = price.toFixed(2)
						self.goods_list.forEach((item, index) => {
							item.products.forEach((product, product_index) => {
								if (product.product_id == goods.product_id) {
									self.$set(product, 'cart_num', product.cart_num - 1)
								}
							});
						})
						self.addclock = false;
					},
					function() {
						self.addclock = false;
					}
				);
			},
			getCart() {
				let id = uni.getStorageSync('user_id');
				if (!id) {
					return
				}
				let self = this;
				self._get('order.cart/lists', {
					shop_supplier_id: self.shop_supplier_id,
					cart_type: 1,
				}, function(res) {
					self.isLoading = true;
					self.cart_total_num = res.data.cart_total_num;
					self.total_price = res.data.total_price;
					self.cart_list = res.data.productList;
				});
			},
			/* 购物车商品添加 */
			cartAdd(goods) {
				let self = this;
				if (self.addclock) {
					return
				}
				self.addclock = true;
				let num = goods.product_num + 1
				let product_id = goods.product_id;
				let total_num = 1;
				self._post('order.cart/sub', {
						product_id: product_id,
						total_num: total_num,
						cart_id: goods.cart_id,
						type: 'up',
						cart_type: 1,
						dinner_type: self.dinner_type
					}, function(res) {
						self.cart_total_num++;
						self.addclock = false;
						let price = self.total_price * 1 + goods.price * 1;
						if (self.bag_type != 1) {
							price += goods.bag_price * 1;
						}
						self.total_price = price.toFixed(2)
						self.goods_list.forEach((item, index) => {
							item.products.forEach((product, product_index) => {
								if (product.product_id == goods.product_id) {
									self.$set(product, 'cart_num', product.cart_num + 1)
								}
							});
						})
						self.$set(goods, 'product_num', num);
						self.$set(goods, 'total_num', goods.total_num + 1);
						self.addclock = false;
					},
					function() {
						self.addclock = false;
					});
			},
			/* 购物车商品减少 */
			cartReduce(goods) {
				let self = this;
				if (self.addclock) {
					return
				}
				self.addclock = true;
				let product_id = goods.product_id;
				let num = goods.product_num;
				self._post(
					'order.cart/sub', {
						product_id: product_id,
						total_num: 1,
						cart_id: goods.cart_id,
						type: 'down',
						cart_type: 1,
						dinner_type: self.dinner_type
					},
					function(res) {
						num--;
						self.cart_total_num--;
						let price = self.total_price * 1 - goods.price * 1;
						if (self.bag_type != 1) {
							price -= goods.bag_price * 1;
						}
						self.total_price = price.toFixed(2)
						self.goods_list.forEach((item, index) => {
							item.products.forEach((product, product_index) => {
								if (product.product_id == goods.product_id) {
									self.$set(product, 'cart_num', product.cart_num - 1)
								}
							});
						})
						self.$set(goods, 'product_num', num);
						self.$set(goods, 'total_num', goods.total_num - 1)
						self.addclock = false;
					},
					function() {
						self.addclock = false;
					}
				);


			},
			//点击菜单项事件
			handleMenuTap(id) {
				if (!this.sizeCalcState) {
					this.calcSize()
				}
				this.currentCateId = id;
				this.$nextTick(() => this.cateScrollTop = this.goods_list.find(item => item.category_id == id).top);
			},
			//商品列表滚动事件
			handleGoodsScroll({
				detail
			}) {
				if (!this.sizeCalcState) {
					this.calcSize()
				}
				const {
					scrollTop
				} = detail;
				let tabs = this.goods_list.filter(item => item.top <= scrollTop).reverse();
				if (tabs.length > 0) {
					this.currentCateId = tabs[0].category_id
				}
			},
			calcSize() {
				let h = 10;
				this.goods_list.forEach(item => {
					let view = uni.createSelectorQuery().select(`#cate-${item.category_id}`)
					view.fields({
						size: true
					}, data => {
						item.top = h;
						h += data.height;
						item.bottom = h;
					}).exec()
				})
				this.sizeCalcState = true
			},
			//打开商品详情模态框
			showGoodDetailModal() {
				this.detail.sku.forEach((item, index) => {
					item.checked = false;
				})
				let obj = {
					specData: this.detail.sku,
					detail: this.detail,
					shop_supplier_id: this.shop_supplier_id,
					productSpecArr: this.specData != null ? new Array(this.specData.spec_attr.length) : [],
					show_sku: {
						sku_image: '',
						seckill_price: 0,
						attr: [],
						product_sku_id: [],
						feed: [],
						line_price: 0,
						seckill_stock: 0,
						seckill_product_sku_id: 0,
						sum: 1
					}
				};
				this.productModel = obj;
				this.goodDetailModalVisible = true;
			},
			//关闭商品详情模态框
			closeGoodDetailModal(num, price) {
				this.goodDetailModalVisible = false
				this.clock = false;
				if (num) {
					this.$set(this.good, 'cart_num', this.good.cart_num ? this.good.cart_num + num : num);
					this.cart_total_num = this.cart_total_num + num;
					this.total_price = this.total_price * 1 + price * 1;
				}
				this.category = {}
				this.good = {}
			},
			//打开/关闭购物车列表popup
			openCartPopup() {
				this.getCart();
				this.cartPopupVisible = !this.cartPopupVisible
			},
			//清空购物车
			handleCartClear() {
				let self = this;
				uni.showModal({
					title: '提示',
					content: '确定清空购物车么',
					success(res) {
						if (res.confirm) {
							self.clearCart()
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					
					}
				})
			},
			clearCart() {
				let self = this;
				self._post(
					'order.cart/delete', {
						shop_supplier_id: self.shop_supplier_id,
						cart_type: 1,
					},
					function(res) {
						self.cartPopupVisible = false;
						self.cart_list = [];
						self.init();
					}
				);
			},
			time_to_sec(time) {
				if (time !== null) {
					var s = "";
					var hour = time.split(":")[0];
					var min = time.split(":")[1];
					s = Number(hour * 3600) + Number(min * 60);
					return s;
				}
			},
			toPay() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				})
				self._get('order.cart/lists', {
					shop_supplier_id: self.shop_supplier_id,
					cart_type: 1,
				}, function(res) {
					self.cart_total_num = res.data.cart_total_num;
					self.total_price = res.data.total_price;
					self.cart_list = res.data.productList;
					let arrIds = [];
					self.cart_list.forEach(item => {
						arrIds.push(item.cart_id);
					});
					if (arrIds.length == 0) {
						uni.showToast({
							title: '请选择商品',
							icon: 'none'
						});
						return false;
					}
					uni.hideLoading()
					uni.navigateTo({
						url: '/pages/order/confirm-order?order_type=cart&cart_ids=' + arrIds.join(',') +
							'&delivery=40&shop_supplier_id=' + self.shop_supplier_id + '&cart_type=1' +
							'&dinner_type=30&table_id=' + self.table_id
					});

				});
			}
		}
	};
</script>

<style lang="scss">
	@import './menu.scss';
</style>
