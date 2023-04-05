<template>
	<view>
		<view class="wrap" v-if="!loading">
			<view class="right" v-if="cart_type==0">
				<view class="takeout" v-for="(item,index) in delivery_set" :key='item' v-if="item=='10'"
					:class="tab_type == 0?'active':''" @click="tabFunc(0)">
					<text>配送订单</text>
				</view>
				<view class="dinein" v-for="(item,index) in delivery_set" :key='item' v-if="item=='20'"
					:class="tab_type == 1?'active':''" @click="tabFunc(1)">
					<text>店内订单</text>
				</view>
			</view>
			<view class="right" v-if="cart_type==1">
				<view class="takeout" v-for="(item,index) in store_set" :key='item' v-if="item=='30'"
					:class="tab_type == 3?'active':''" @click="tabFunc(3)">
					<text>打包订单</text>
				</view>
				<view class="dinein" v-for="(item,index) in store_set" :key='item' v-if="item=='40'"
					:class="tab_type == 4?'active':''" @click="tabFunc(4)">
					<text>店内订单</text>
				</view>
			</view>
			<view class="header" v-if="cart_type==0">
				<view class="headr_top">
					<view class="flex-1" style="width: 100%;" v-if="tab_type != 1">
						<view class="left overflow-hidden">
							<view class="overflow-hidden f28 fb w-b-a" style="width: 600rpx;"
								@click="gotoPage('/pages/user/address/storeaddress?shop_supplier_id='+options.shop_supplier_id)">
								<template v-if="Address!=null">
									{{Address.detail+Address.address+" "+Address.name+" "+Address.phone}}
								</template>
								<template v-else>
									请选择配送地址
								</template>
							</view>
						</view>
					</view>
					<view class="header_bottom" v-if="tab_type == 1">
						<view class="uni-list-cell-left f32 fb">
							<input type="number" v-model="phone" placeholder="请输入联系电话" />
						</view>
					</view>
				</view>
			</view>
			<view class="d-b-c meal_item" @click="timepick()" v-if="tab_type == 0 && delivery != 10">
				<view class="f28">取餐时间</view>
				<view class="uni-list">
					<view class="uni-list-cell">
						<view class="uni-list-cell-left f28 ">
							<text class="f32 fb ">{{mealtime}}</text><text class="icon iconfont icon-jiantou"></text>
						</view>
					</view>
				</view>
			</view>
			<view class="d-b-c meal_item" @click="timepick()" v-if="tab_type == 0&& delivery == 10">
				<view class="f28">预计送达时间：</view>
				<view class="uni-list">
					<view class="uni-list-cell">
						<view class="uni-list-cell-left f28 ">
							<text class="blue">{{wmtime}}</text><text class="icon iconfont icon-jiantou"></text>
						</view>
					</view>
				</view>
			</view>

			<!--购买的产品-->
			<view class="vender">
				<view class="left">
					<view class="store-name">
						<text>{{supplier.name}}</text>
					</view>
				</view>
				<view class="list">
					<view class="item d-b-c" v-for="(item, index) in ProductData" :key="index">
						<view class="info d-s-s">
							<view class="cover">
								<image :src="item.image.file_path" mode=""></image>
							</view>
							<view>
								<view class="title f30 fb mb16">
									{{item.product.product_name}}
								</view>
								<view class="num-wrap pl-30 gray9 f22">
									{{ item.describe }}
								</view>
							</view>
						</view>
						<view class="" style="height: 148rpx;">
							<view class="f32 order_item mb16">¥{{ item.price}}</view>
							<view class="f22 order_item ">×{{ item.product_num }}</view>
						</view>
					</view>
				</view>
				<!--总数-->

				<view class="other_box">
					<view class="item">
						<text class="key">商品小计：</text>
						<text class="f32">￥{{ OrderData.order_price }}</text>
					</view>
					<view class="item" v-if="tab_type != 1&&OrderData.express_price!=0">
						<text class="key">配送费用：</text>
						<text class="f32">￥{{ OrderData.express_price }}</text>
					</view>
					<view class="item">
						<text class="key">包装费用：</text>
						<text class="f32">￥{{ OrderData.order_bag_price }}</text>
					</view>
				</view>
				<view class="total-box">
					共2件商品 小计
					<text class="f32 fb ml15">￥{{ OrderData.order_total_price }}</text>
				</view>
			</view>
			<view class="meal_item">
				<view class="d-b-c item">
					<view class="mr20">备注:</view>
					<input class="flex-1" type="text" v-model="remark" placeholder="请填写您的其他要求" />
				</view>
			</view>
			<!--支付方式-->
			<view class="buy-checkout" v-if="OrderData.order_pay_price != 0">
				<view :class="pay_type == 20 ? 'item active' : 'item'" @click="payTypeFunc(20)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-weixin"></span></view>
						<text class="key">微信支付：</text>
					</view>
					<view class="icon-box d-c-c"><span class="icon iconfont icon-xuanze"></span></view>
				</view>
				<!-- #ifdef MP-ALIPAY -->
				<view :class="pay_type == 30 ? 'item active' : 'item'" @click="payTypeFunc(30)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-zhifubao"></span></view>
						<text class="key">支付宝支付：</text>
					</view>
					<view class="icon-box d-c-c"><span class="icon iconfont icon-xuanze"></span></view>
				</view>
				<!-- #endif -->
				<view :class="pay_type == 10 ? 'item active' : 'item'" @click="payTypeFunc(10)">
					<view class="d-s-c">
						<view class="icon-box d-c-c mr10"><span class="icon iconfont icon-yue"></span></view>
						<text class="key">余额支付：</text>
					</view>
					<view class="icon-box d-c-c"><span class="icon iconfont icon-xuanze"></span></view>
				</view>
			</view>
			<!--底部支付-->
			<view class="foot-pay-btns">
				<view class="price" v-if="!OrderData.force_points">
					¥
					<text class="num">{{ OrderData.order_pay_price }}</text>
				</view>
				<button type="primary" @click="SubmitOrder">提交订单</button>
			</view>
			<timepicker :isTimer='isTimer' @close='closetimer'></timepicker>
		</view>
	</view>
</template>

<script>
	import Myinfo from './confirm-order/my-info';
	import Storeinfo from './confirm-order/store-info';
	import timepicker from '@/components/timepicker/timepicker';

	import {
		pay
	} from '@/common/pay.js';
	export default {
		components: {
			Myinfo,
			Storeinfo,
			timepicker
		},
		data() {
			return {
				/*是否加载完成*/
				loading: true,
				options: {},
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				tab_type: 0,
				/*商品id*/
				product_id: '',
				/*商品数量*/
				product_num: '',
				/*商品数据*/
				ProductData: [],
				/*订单数据数据*/
				OrderData: [],
				// 是否存在收货地址
				exist_address: false,
				/*默认地址*/
				Address: {
					region: []
				},
				extract_store: {},
				last_extract: {},
				product_sku_id: 0,
				/*配送方式*/
				/* 10配送20自提30打包40店内 */
				delivery: 0,
				/*自提店id*/
				store_id: 1,
				/*是否使用积分*/
				is_use_points: 0,
				linkman: '',
				phone: '',
				remark: '',
				/*支付方式*/
				pay_type: 20,
				deliverySetting: [],
				/*消息模板*/
				temlIds: [],
				takeout_address: {},
				isTimer: false,
				mealtime: '',
				wmtime: '',
				is_pack: 1,
				supplier: {},
				dinner_type: 10,
				cart_type: 0,
				store_set: [],
				delivery_set: [],
				table_id: 0,
				min_money: 0
			};
		},
		onLoad(options) {
			let self = this;
			self.options = options;
			self.cart_type = options.cart_type;
			self.table_id = options.table_id || 0;
			self.dinner_type = options.dinner_type;
			self.delivery = options.delivery;
			//#ifdef MP-ALIPAY	
			self.pay_type = 30;
			//#endif
			// this.getData();
		},
		onShow() {
			this.$fire.on('takeout', function(e) {
				self.takeout_address = e;
				self.orderType = 'takeout'
			})
			this.getData();
		},
		methods: {
			/**/
			hasType(e) {
				if (this.deliverySetting.indexOf(e) != -1) {
					return true;
				} else {
					return false;
				}
			},
			/*支付方式选择*/
			payTypeFunc(e) {
				this.pay_type = e;
			},
			/*是否使用积分选择*/
			onShowPoints: function(e) {
				let self = this;
				if (e.target.value == true) {
					self.is_use_points = 1;
				} else {
					self.is_use_points = 0;
				}
				self.getData();
			},
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				let callback = function(res) {
					self.OrderData = res.data.orderInfo;
					self.min_money = res.data.orderInfo.supplier.min_money;
					self.temlIds = res.data.template_arr;
					self.exist_address = self.OrderData.exist_address;
					self.Address = self.OrderData.address;
					self.extract_store = self.OrderData.extract_store;
					self.last_extract = self.OrderData.last_extract;
					self.ProductData = self.OrderData.product_list;
					self.supplier = res.data.orderInfo.supplier;
					self.linkman = res.data.orderInfo.last_extract.linkman;
					self.phone = res.data.orderInfo.last_extract.phone;
					self.delivery_set = res.data.orderInfo.supplier.delivery_set;
					self.store_set = res.data.orderInfo.supplier.store_set;
					if (self.cart_type == 0) {
						if (self.delivery_set.indexOf(self.delivery) == -1) {
							if (self.delivery_set[0] == '10') {
								self.tabFunc(0, true)
							} else {
								self.tabFunc(1, true)
							}
						}

					} else {
						if (self.store_set.indexOf(self.delivery) == -1) {
							console.log(self.store_set)
							console.log(self.store_set.indexOf(self.delivery))
							console.log(self.store_set[0] == '30')
							if (self.store_set[0] == '30') {
								self.tabFunc(3, true)
							} else {
								self.tabFunc(4, true)
							}
						}

					}
					let myDate = new Date();
					let myhours = myDate.getHours(); //获取当前小时数(0-23)
					if (myhours < 10) {
						myhours = '0' + myhours
					}
					let myminute = myDate.getMinutes(); //获取当前分钟数(0-59)
					if (myminute < 10) {
						myminute = "0" + myminute
					}
					let wmhours = myDate.getHours();
					let wmminute = myDate.getMinutes() + 15;
					if (wmminute >= 60) {
						wmminute = wmminute - 60;
						wmhours = wmhours + 1;
					}
					if (wmminute < 10) {
						wmminute = '0' + wmminute;
					}
					if (wmhours < 10) {
						wmhours = '0' + wmhours;
					}
					self.wmtime = wmhours + ':' + wmminute;
					self.mealtime = myhours + ':' + myminute
					if (self.OrderData.delivery == '20') {
						self.tab_type = 1;
					}
					if (self.OrderData.delivery == '40') {
						self.tab_type = 4;
					}
					self.deliverySetting = self.OrderData.deliverySetting;
					if (self.OrderData.order_pay_price == 0) {
						self.pay_type = 10;
					}

					//#ifdef H5
					if (!self.isWeixin() && res.data.h5_alipay) {
						self.showAlipay = true;
					}
					//#endif
					self.loading = false;
				};

				// 请求的参数
				let params = {
					delivery: self.delivery,
					store_id: 1,
					is_use_points: self.is_use_points,
					pay_source: self.getPlatform(),
					mealtime: '',
				};

				//直接购买
				if (self.options.order_type === 'buy') {
					self._get(
						'order.order/buy',
						Object.assign({}, params, {
							product_id: self.options.product_id,
							product_num: self.options.product_num,
							product_sku_id: self.options.product_sku_id
						}),
						function(res) {
							callback(res);
						},
					);
				}
				// 购物车结算
				else if (self.options.order_type === 'cart') {
					self._get(
						'order.order/cart',
						Object.assign({}, params, {
							cart_ids: self.options.cart_ids || 0,
							shop_supplier_id: self.options.shop_supplier_id || 0,
							order_type: self.options.cart_type,
							table_id: self.table_id
						}),
						function(res) {
							callback(res);
						},
						function(err) {
							if (self.tab_type == 1) {
								self.tabFunc(0)
							} else if (self.tab_type == 0) {
								self.tabFunc(1)
							}
						}
					);
				}
				// 积分兑换结算
				else if (self.options.order_type == 'points') {
					self._get(
						'plus.points.order/buy',
						Object.assign({}, params, {
							point_product_id: self.options.point_product_id,
							product_sku_id: self.options.product_sku_id,
							point_product_sku_id: self.options.point_product_sku_id,
							product_num: self.options.product_num
						}),
						function(res) {
							callback(res);
						}
					);
				}
				// 限时秒杀
				else if (self.options.order_type === 'seckill') {
					params.num = self.options.num;
					self._get(
						'plus.seckill.order/buy',
						Object.assign({}, params, {
							seckill_product_id: self.options.seckill_product_id,
							product_sku_id: self.options.product_sku_id,
							seckill_product_sku_id: self.options.seckill_product_sku_id,
							product_num: self.options.product_num
						}),
						function(res) {
							callback(res);
						}
					);
				}
				//砍价
				else if (self.options.order_type === 'bargain') {
					self._get(
						'plus.bargain.order/buy',
						Object.assign({}, params, {
							bargain_product_id: self.options.bargain_product_id,
							product_sku_id: self.options.product_sku_id,
							bargain_product_sku_id: self.options.bargain_product_sku_id,
							bargain_task_id: self.options.bargain_task_id
						}),
						function(res) {
							callback(res);
						}
					);
				}
				//拼团
				else if (self.options.order_type === 'assemble') {
					self._get(
						'plus.assemble.order/buy',
						Object.assign({}, params, {
							assemble_product_id: self.options.assemble_product_id,
							product_sku_id: self.options.product_sku_id,
							assemble_product_sku_id: self.options.assemble_product_sku_id,
							product_num: self.options.product_num,
							assemble_bill_id: self.options.assemble_bill_id,
						}),
						function(res) {
							callback(res);
						}
					);
				}
			},

			/*选择配送方式*/
			tabFunc(e, flag) {
				if (e == 0) {
					if (this.min_money * 1 > this.OrderData.order_pay_price * 1) {
						this.showError('配送最低费用')
						return
					}
				}
				/* 0外卖 1自取  3打包 4店内 */
				this.tab_type = e;
				if (e == 0) {
					this.delivery = '10';
					this.dinner_type = 10;
				} else if (e == 1) {
					this.delivery = '20';
					this.dinner_type = 20;
				} else if (e == 3) {
					this.delivery = '30';
					this.dinner_type = 30;
				} else if (e == 4) {
					this.delivery = '40';
					this.dinner_type = 30;
				}
				if (!flag) {
					this.getData();
				}
			},

			/*提交订单*/
			SubmitOrder() {
				let self = this;
				uni.showLoading({
					title: '加载中',
					mask: true
				});
				let _linkman = null;
				let _phone = null;
				if (self.$refs != null) {
					if (self.$refs.getShopinfoData != null) {
						_phone = self.$refs.getShopinfoData.phone;
						_linkman = self.$refs.getShopinfoData.linkman;
					}
				}

				let params = {
					delivery: self.delivery,
					store_id: 1,
					is_use_points: 0,
					linkman: self.linkman,
					phone: self.phone,
					remark: self.remark,
					pay_type: self.pay_type,
					pay_source: self.getPlatform(),
					mealtime: self.mealtime,
					shop_supplier_id: self.options.shop_supplier_id
				};
				if (self.delivery == 10) {
					params.mealtime = self.wmtime
				}
				// 创建订单-直接下单
				let url = '';
				if (self.options.order_type === 'buy') {
					url = 'order.order/buy';
					params = Object.assign(params, {
						product_id: self.options.product_id,
						product_num: self.options.product_num,
						product_sku_id: self.options.product_sku_id
					});
				}

				// 创建订单-购物车结算
				if (self.options.order_type === 'cart') {
					url = 'order.order/cart';
					params = Object.assign(params, {
						cart_ids: self.options.cart_ids || 0,
						dinner_type: self.dinner_type,
						shop_supplier_id: self.options.shop_supplier_id || 0,
						order_type: self.options.cart_type,
						table_id: self.table_id
					});
				}

				// 创建订单-积分兑换
				if (self.options.order_type === 'points') {
					url = 'plus.points.order/buy';
					params = Object.assign(params, {
						point_product_id: self.options.point_product_id,
						product_sku_id: self.options.product_sku_id,
						point_product_sku_id: self.options.point_product_sku_id,
						product_num: self.options.product_num
					});
				}
				// 创建订单-限时秒杀
				if (self.options.order_type === 'seckill') {
					url = 'plus.seckill.order/buy';
					params.num = self.options.num;
					params = Object.assign(params, {
						seckill_product_id: self.options.seckill_product_id,
						product_sku_id: self.options.product_sku_id,
						seckill_product_sku_id: self.options.seckill_product_sku_id,
						product_num: self.options.product_num
					});
				}
				// 创建订单-砍价
				if (self.options.order_type === 'bargain') {
					url = 'plus.bargain.order/buy';
					params = Object.assign(params, {
						bargain_product_id: self.options.bargain_product_id,
						product_sku_id: self.options.product_sku_id,
						bargain_product_sku_id: self.options.bargain_product_sku_id,
						bargain_task_id: self.options.bargain_task_id
					});
				}

				// 创建订单-限时拼团
				if (self.options.order_type === 'assemble') {
					url = 'plus.assemble.order/buy';
					params = Object.assign(params, {
						assemble_product_id: self.options.assemble_product_id,
						product_sku_id: self.options.product_sku_id,
						assemble_product_sku_id: self.options.assemble_product_sku_id,
						assemble_bill_id: self.options.assemble_bill_id,
						product_num: self.options.product_num,
					});
				}
				let callback = function() {
					self._post(url, params, function(result) {
						pay(result, self);
					});
				};

				self.subMessage(self.temlIds, callback);
			},
			timepick() {
				this.isTimer = true
			},
			closetimer(e) {
				if (e != '') {
					this.wmtime = e;
					this.mealtime = e;
				}
				this.isTimer = false
			},
			packTypeFunc(n) {
				this.is_pack = n;
			}
		}
	};
</script>

<style lang="scss">
	.headr_top {
		height: 140rpx;
	}

	.header {
		width: 100%;
		box-sizing: border-box;
		padding: 35rpx;
		background-color: #ffffff;
		// box-shadow: 0 0 0.06rem 0 rgba(0, 0, 0, 0.1);
		border-radius: 30rpx;
		overflow: hidden;
		position: relative;
		z-index: 20;
	}

	.left {
		flex: 1;
		display: flex;
		flex-direction: column;

		.store-name {
			display: flex;
			justify-content: flex-start;
			align-items: center;
			font-size: 26rpx;
			margin-bottom: 30rpx;

			.iconfont {
				margin-left: 10rpx;
				line-height: 100%;
			}
		}

		.store-location {
			display: flex;
			justify-content: flex-start;
			align-items: center;
			color: $text-color-assist;
			font-size: $font-size-sm;

			.iconfont {
				vertical-align: middle;
				display: table-cell;
				color: $color-primary;
				line-height: 100%;
			}
		}
	}

	.wrap {
		padding: 25rpx;
		padding-bottom: 140rpx;
		background: linear-gradient(180deg, $dominant-color 0, rgba(255, 204, 0, 0.09) 100%)
	}

	.icon-box .icon-zhifubao {
		color: #1296db;
		font-size: 50rpx;
	}

	.order_item {
		width: 150rpx;
		text-align: right;
	}

	.other_box {
		border-radius: 0;
		box-shadow: 0;
	}

	.other_box .item {
		height: 88rpx;
		box-sizing: border-box;
		line-height: 88rpx;
		padding-right: 24rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		border-bottom: 1rpx solid #D9D9D9;

	}

	.header_bitem {}

	.f-r {
		float: right;
	}

	.meal_item {
		margin: 26rpx 0;
		height: 96rpx;
		line-height: 96rpx;
		background-color: #FFFFFF;
		border-radius: 10rpx;
		padding-left: 42rpx;
		padding-right: 30rpx;

		.icon-jiantou {
			font-size: 22rpx;
			margin-left: 26rpx;
		}
	}

	.pack_item {
		text-align: center;
		height: 80rpx;
		line-height: 80rpx;
	}

	.pack_item.active .icon-xuanze {
		color: $color-primary;
	}

	.right {
		border-radius: 30rpx;
		display: flex;
		align-items: center;
		font-size: 32rpx;
		font-weight: bold;
		color: #282828;
		height: 68rpx;
		width: 100%;
		position: relative;
		margin-top: 34rpx;

		.dinein,
		.takeout {
			position: relative;
			display: flex;
			align-items: flex-start;
			width: 344rpx;
			height: 150rpx;
			box-sizing: border-box;
			padding-top: 25rpx;
			position: absolute;
			z-index: 0;
			top: -18rpx;
			background-color: #ffffff60;

			&.active {
				z-index: 1;
				width: 490rpx;
				height: 150rpx;
				color: $dominant-color;
				background-color: #ffffff !important;
				flex-shrink: 0;
			}
		}

		.takeout {
			justify-content: flex-start;

			padding-left: 78rpx;
			left: 0;
			border-top-left-radius: 30rpx;

			&.active {
				padding-left: 153rpx;
				border-top-right-radius: 150rpx;
				border-top-left-radius: 30rpx;
			}

		}

		.dinein {
			right: 0;
			justify-content: flex-end;
			padding-right: 65rpx;
			border-top-right-radius: 30rpx;

			&.active {
				padding-right: 150rpx;
				border-top-left-radius: 150rpx;
				border-top-right-radius: 30rpx;

			}

		}
	}
</style>
