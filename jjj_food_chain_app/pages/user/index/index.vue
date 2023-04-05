<template>
	<view class="user-index">
		<!-- #ifdef APP-PLUS -->
		<header-bar></header-bar>
		<!-- #endif -->
		<!--个人信息-->
		<view v-if="!loadding">
			<view class="user-header pr">
				<image class="bg-img" src="../../../static/bg-user.png" mode=""></image>
				<view class="user-header-inner">
					<view class="user-info">
						<view class="photo">
							<image :src="detail.avatarUrl" mode="aspectFill"></image>
						</view>
						<view class="info d-c d-c-s">
							<view class="d-c-c">
								<view class="name">{{ detail.nickName }}</view>
							</view>
							<view class="tel d-s-c">
								<text class="f26">ID:{{ detail.user_id }}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<!--我的订单-->
			<view class="my-order">
				<view class="order-top">
					<view class="order-title">我的订单</view>
					<view class="order-all" @click="gotoPage('/pages/order/myorder')">全部订单<text
							class="icon iconfont icon-jiantou"></text></view>
				</view>
				<view class="list d-a-c flex-1">
					<view class="item d-c-c d-c" @click="gotoPage('/pages/user/my-wallet/my-wallet')">
						<view class=" red_mini">{{ detail.balance }}</view>
						<text class="pt16 f28 gray3">金额<text class="f22 gray9">(元)</text></text>
					</view>
				</view>
			</view>
			<view class="bind_phone" v-if="!detail.mobile">
				<view class="bind_content">
					<view class="bind_txt">点击绑定手机号，确保账户安全</view>
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="getPhoneNumber" class="bind_btn theme-btn"
						@getphonenumber="getPhoneNumber">去绑定</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<button class="bind_btn theme-btn" @click="bindMobile">去绑定</button>
					<!-- #endif -->
				</view>
			</view>
			<!--菜单-->
			<view class="menu-wrap">
				<view class="order-top">
					<view class="order-title">服务功能</view>
				</view>
				<view class="group-bd f-w">
					<view class="item" v-for="(item, index) in menus" :key="index"
						@click="jumpPage(item.link_url)">
						<view class="icon-round d-c-c">
							<image class="icon-round" :src="item.image_url" mode=""></image>
						</view>
						<text class="name">{{ item.title }}</text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import utils from '@/common/utils.js';
	export default {
		data() {
			return {
				isloadding: true,
				/*签到数据*/
				sign: {},
				/*是否加载完成*/
				loadding: true,
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				/*菜单*/
				menus: [],
				detail: {
					balance: 0,
					points: 0,
					grade: {
						name: ''
					},
				},
				orderCount: {},
				setting: {},
				user_type: '', //用户状态
				msgcount: 0, //用户未读消息
			};
		},
		onPullDownRefresh() {
			let self = this;
			self.getData();
		},
		onShow() {
			/*获取个人中心数据*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				self.isloadding = true;
				self._get('user.index/detail', {
					url: self.url
				}, function(res) {
					//#ifdef MP-WEIXIN
					if (res.data.getPhone) {
						self.gotoPage('/pages/login/bindmobile');
						return;
					}
					//#endif
					self.detail = res.data.userInfo;
					self.sign = res.data.sign;
					self.menus = res.data.menus;
					self.setting = res.data.setting;
					self.loadding = false;
					uni.stopPullDownRefresh();
					self.isloadding = false;
				});
			},
			bindMobile() {
				this.gotoPage('/pages/user/modify-phone/modify-phone');
			},
			/*跳转页面*/
			jumpPage(path) {
				let self = this;
				if (path.startsWith('/')) {
					self.gotoPage(path);
				} else {
					self[path]();
				}
			},
			/*扫一扫核销*/
			scanQrcode: function() {
				this.gotoPage('/pages/user/scan/scan');
			},
			getPhoneNumber(e) {
				var self = this;
				if (e.detail.errMsg !== 'getPhoneNumber:ok') {
					return false;
				}
				uni.showLoading({
					title: '加载中'
				})
				uni.login({
					success(res) {
						// 发送用户信息
						self._post('user.user/bindMobile', {
							code: res.code,
							encrypted_data: encodeURIComponent(e.detail.encryptedData),
							iv: encodeURIComponent(e.detail.iv),
						}, result => {
							uni.showToast({
								title: '绑定成功'
							});
							self.getData()
						}, false, () => {
							uni.hideLoading()
						});
					}
				});
			},
		}
	};
</script>

<style lang="scss">
	page {
		background-color: #EBEBEB;
	}

	.w100 {
		width: 100%;
	}

	.foot_ {
		height: 98rpx;
		width: 100%;
	}

	.user-header {
		position: relative;
		height: 292rpx;
		z-index: 1;
		.bg-img{
			position: absolute;
			left: 0;
			top: 0;
			z-index: 1;
			width: 100%;
			height: 292rpx;
		}
	}

	.user-header .user-header-inner {
		position: relative;
		padding: 30rpx 30rpx 120rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		overflow: hidden;
		z-index: 1;
	}


	.user-header .user-header-inner::before {
		width: 200rpx;
		height: 200rpx;
		left: -60rpx;
		top: -20rpx;
		background-image: radial-gradient(-90deg, rgba(255, 255, 255, 0.2) 10%, rgba(255, 255, 255, 0));
	}

	.user-header .user-info {
		display: flex;
		justify-content: flex-start;
		align-items: center;
	}

	.user-header .photo,
	.user-header .photo image {
		width: 100rpx;
		height: 100rpx;
		border-radius: 50%;
	}

	.user-header .photo {
		border: 4rpx solid #ffffff;
	}

	.user-header .info {
		padding-left: 20rpx;
		box-sizing: border-box;
		overflow: hidden;
		color: #ffffff;
	}

	.user-header .info .name {
		font-weight: bold;
		font-size: 32rpx;
	}

	.user-header .info .tel {
		font-size: 26rpx;
	}

	.user-header .info .grade {
		display: block;
		padding: 4px 16rpx;
		font-size: 22rpx;
		/* height: 36rpx; */
		line-height: 36rpx;
		border-radius: 40rpx;
		font-family: PingFang SC;
	}

	.order_center {
		border-left: 1rpx solid #D9D9D9;
		border-right: 1rpx solid #D9D9D9;
	}

	.menu-wrap {
		margin: 0 26rpx;
		margin-top: 26rpx;
		background: #ffffff;
		border-radius: 20rpx;

		.order-top {
			display: flex;
			justify-content: space-between;
			align-items: center;
			height: 94rpx;
			padding-left: 46rpx;
			padding-right: 35rpx;
			box-sizing: border-box;
			border-bottom: 1rpx solid #EEEEEE;
			margin-left: 35rpx;

			.order-title {
				position: relative;
				font-size: 28rpx;
			}

			.order-title::after {
				content: '';
				width: 10rpx;
				height: 27rpx;
				background: #FFCC00;
				border-radius: 5rpx;
				position: absolute;
				top: 0;
				bottom: 0;
				left: -47rpx;
				margin: auto;
			}

			.order-all {
				color: #28282870;
			}

			.icon.iconfont {
				color: #a0a0a0;
				font-size: 22rpx;
			}

		}
	}

	.menu-wrap .group-bd {
		display: flex;
		justify-content: flex-start;
		align-items: flex-start;
	}

	.menu-wrap .item {
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
		width: 25%;
		height: 150rpx;
		font-size: 24rpx;
		margin: 28rpx 0;
	}

	.menu-wrap .icon-round {
		width: 90rpx;
		height: 90rpx;
		color: #ffffff;
	}

	.menu-wrap .item .iconfont {
		font-size: 40rpx;
		color: #ffffff;
	}

	.menu-wrap .item .name {
		margin-top: 19rpx;
	}

	.bind_phone {
		width: 100%;
		height: 80rpx;
		padding: 0 26rpx;
		box-sizing: border-box;
		margin-bottom: 30rpx;
	}

	.bind_content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		background: #ffffff;
		/* box-shadow: 0 0 6rpx 0 rgba(0, 0, 0, 0.1); */
		border-radius: 16rpx;
		height: 100%;
		padding: 0 20rpx;
	}

	.bind_btn {
		width: 134rpx;
		height: 50rpx;
		line-height: 50rpx;
		font-size: 22rpx;
		border-radius: 25rpx;
		text-align: center;
	}

	.my-order {
		margin: 0 24rpx;
		padding: 0 35rpx;
		box-sizing: border-box;
		border-radius: 16rpx;
		background: #ffffff;
		margin-bottom: 26rpx;
		margin-top: -112rpx;
		position: relative;
		z-index: 1;
		.order-top {
			display: flex;
			justify-content: space-between;
			align-items: center;
			height: 94rpx;
			padding-left: 46rpx;
			box-sizing: border-box;
			border-bottom: 1rpx solid #EEEEEE;

			.order-title {
				position: relative;
				font-size: 28rpx;
			}

			.order-title::after {
				content: '';
				width: 10rpx;
				height: 27rpx;
				background: #FFCC00;
				border-radius: 5rpx;
				position: absolute;
				top: 0;
				bottom: 0;
				left: -47rpx;
				margin: auto;
			}

			.order-all {
				color: #28282870;
			}

			.icon.iconfont {
				color: #a0a0a0;
				font-size: 22rpx;
			}

		}

		.red_mini {
			font-size: 45rpx;
			color: #282828;
			font-weight: 800;
		}
	}


	.my-order .item {
		display: flex;
		margin: 20rpx 0;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 26rpx;
		flex: 1;
	}

	.my-order .icon-box {
		width: 60rpx;
		height: 60rpx;
	}

	.my-order .icon-box .iconfont {
		font-size: 50rpx;
		color: #333333;
	}
</style>
