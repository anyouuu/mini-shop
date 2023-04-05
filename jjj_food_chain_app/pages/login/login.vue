<template>
	<view class="login-container">

		<view class="wechatapp">
			<!-- #ifdef MP-WEIXIN -->
			<view class="header">
				<open-data class="" type="userAvatarUrl"></open-data>
			</view>
			<!-- #endif -->
			<!-- #ifndef MP-WEIXIN -->
			<image style="width: 190rpx;height: 190rpx;" src="/static/nav_03.png"></image>
			<!-- #endif -->
		</view>
		<view class="auth-title">申请获取以下权限</view>
		<view class="auth-subtitle">获得你的公开信息（昵称、头像等）</view>
		<view class="login-btn">
			<!-- #ifdef MP-WEIXIN -->
			<button class="btn-normal" @click="getUserInfo">授权登录</button>
			<!-- #endif -->
		</view>
		<view class="no-login-btn">
			<button class="btn-normal" @click="onNotLogin">暂不登录</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				background: '',
				listData: []
			}
		},
		onShow(){
			//wx.login(); //重新登录
		},
		onLoad() {

		},
		methods: {
			/*改变发送验证码按钮文本*/
			changeMsg() {
				if (this.second > 0) {
					this.send_btn_txt = this.second + '秒';
					this.second--;
					setTimeout(this.changeMsg, 1000);
				} else {
					this.send_btn_txt = '获取验证码';
					this.second = 60;
					this.is_send = false;
				}
			},
			onNotLogin:function(){
				this.gotoPage('/pages/index/index')
			},
			getUserInfo: function() {
				let self = this;
				wx.getUserProfile({
					lang: 'zh_CN',
					desc: '用于完善会员资料', 
					success: (res) => {
						if (res.errMsg !== 'getUserProfile:ok') {
							return false;
						}
						uni.showLoading({
							title: "正在登录",
							mask: true
						});
						// 执行微信登录
						wx.login({
							success(res_login) {
								// 发送用户信息
								self._post('user.user/login', {
									code: res_login.code,
									user_info: res.rawData,
									encrypted_data: encodeURIComponent(res.encryptedData),
									iv: encodeURIComponent(res.iv),
									signature: res.signature,
									referee_id: uni.getStorageSync('referee_id'),
									source: 'wx'
								}, result => {
									// 记录token user_id
									uni.setStorageSync('token', result.data.token);
									uni.setStorageSync('user_id', result.data.user_id);
									// 执行回调函数
									uni.navigateBack();
								}, false, () => {
									uni.hideLoading();
								});
							}
						});
					}
				});
			},
			onGetAuthorize(res) {
				console.log('开始授权')
				let self = this;
				uni.login({
					provider: 'alipay',
					success: function(loginRes) {
						console.log("sucss")
						console.log(loginRes)
						uni.getUserInfo({
							provider: 'alipay',
							success: function(infoRes) {
								self.aliPayLogin(loginRes, infoRes);
							}
						});
					},
					fail(err) {
						console.log(err)
					}
				});
			},
			aliPayLogin(loginRes, infoRes) {
				let self = this;
				console.log(loginRes); // 获取用户信息 
				console.log(infoRes);
				uni.showLoading({
					title: '登录中',
					mask: true
				});
				self._post('user.useralipay/login', {
					code: loginRes.authCode,
					avatar: infoRes.avatar,
					nickName: infoRes.nickName
				}, result => {
					// 记录token user_id
					uni.setStorageSync('token', result.data.token);
					uni.setStorageSync('user_id', result.data.user_id);
					// 执行回调函数
					uni.navigateBack();
				}, false, () => {
					uni.hideLoading();
				});
			},
		},
	}
</script>

<style>
	.login-container {
		padding: 30rpx;
	}

	.wechatapp {
		padding: 80rpx 0 48rpx;
		border-bottom: 1rpx solid #e3e3e3;
		margin-bottom: 72rpx;
		text-align: center;
	}

	.wechatapp .header {
		width: 190rpx;
		height: 190rpx;
		border: 2px solid #fff;
		margin: 0rpx auto 0;
		border-radius: 50%;
		overflow: hidden;
		box-shadow: 1px 0px 5px rgba(50, 50, 50, 0.3);
	}

	.auth-title {
		color: #585858;
		font-size: 34rpx;
		margin-bottom: 40rpx;
	}

	.auth-subtitle {
		color: #888;
		margin-bottom: 88rpx;
		font-size: 28rpx;
	}

	.login-btn {
		padding: 0 20rpx;
	}

	.login-btn button {
		height: 88rpx;
		line-height: 88rpx;
		background: #04be01;
		color: #fff;
		font-size: 30rpx;
		border-radius: 999rpx;
		text-align: center;
	}

	.no-login-btn {
		margin-top: 20rpx;
		padding: 0 20rpx;
	}

	.no-login-btn button {
		height: 88rpx;
		line-height: 88rpx;
		background: #dfdfdf;
		color: #fff;
		font-size: 30rpx;
		border-radius: 999rpx;
		text-align: center;
	}
</style>
