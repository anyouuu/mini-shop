<template>
	<view class="address-form">
		<view class="bg-white p-0-30 f30">
			<view class="d-b-c p-30-0 border-b">
				<text class="key-name">会员ID</text>
				<view class="d-e-c">
					<text class="mr20">{{userInfo.user_id}}</text>
				</view>
			</view>
			<view class="d-b-c p-30-0 border-b">
				<text class="key-name">昵称</text>
				<view class="d-e-c">
					<text class="mr20">{{userInfo.nickName}}</text>
				</view>
			</view>
			<view class="d-b-c p-30-0">
				<text class="key-name">手机号码</text>
				<view class="d-e-c" v-if="userInfo.mobile">
					<text class="mr20">{{userInfo.mobile}}</text>
				</view>
				<view class="d-e-c" v-if="!userInfo.mobile">
					<text class="mr20">未绑定</text>
				</view>
			</view>
			<view class="d-b-c p-30-0">
				<text class="key-name">出生日期</text>
				<view class="d-e-c" v-if="userInfo.birthday">
					<text class="mr20">{{userInfo.birthday}}</text>
				</view>
				<view class="d-e-c" v-if="!userInfo.birthday" @click="Bindbirthday">
					<text class="mr20">未绑定</text>
					<text class="iconfont icon-jiantou"></text>
				</view>
			</view>
			<view class="setup-btn" @tap="logout()">退出登录</view>
		</view>
		<!-- 修改资料 -->
		<Popup :show="isPopup" type='bottom' :width="750" :padding="0" @hidePopup="hidePopupFunc">
			<view class="d-s-s d-c p20 mpservice-wrap">
				<view class="tc f32 fb ww100">修改</view>
				<view  class="ww100">
					<picker class="datapicker ww100" mode="date" :value="birthday" @change="fbindDateChange">
						<view class="make-item pop-input input-box f28">{{birthday || '请选择出生日期'}}</view>
					</picker>
				</view>
				<view class="red">注：仅可修改一次</view>
				<view class="d-a-c ww100">
					<button class="btn-gray-border" @click="hidePopupFunc">取消</button>
					<button class="btn-gcred" @click="subBirthday">确认</button>
				</view>
			</view>
		</Popup>
	</view>
</template>

<script>
	import Popup from '@/components/uni-popup.vue';
	import {
		gotopage
	} from '@/common/gotopage.js';
	export default {
		components: {
			Popup
		},
		data() {
			return {
				userInfo: {},
				isPopup:false,
				birthday:''
			};
		},
		onShow() {
			/*获取个人中心数据*/
			this.getData();
		},
		methods: {
			/*获取数据*/
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._get('user.index/setting', {}, function(res) {
					self.userInfo = res.data.userInfo;
					uni.hideLoading();
				});
			},
			gotoBind() {
				uni.navigateTo({
					url: "/pages/user/modify-phone/modify-phone"
				});
			},
			subBirthday(){
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._post('user.user/updateInfo', {
					birthday:self.birthday
				}, function(res) {
					self.userInfo.birthday;
					uni.hideLoading();
					self.showSuccess('修改成功',function(){
						this.isPopup=false;
						self.getData()
					})
				});
			},
			hidePopupFunc(){
				this.isPopup=false;
			},
			fbindDateChange(e) {
				
				this.birthday = e.detail.value;
			},
			logout() {
				uni.removeStorageSync('token');
				uni.removeStorageSync('user_id');
				this.gotoPage('/pages/index/index');
			},
			Bindbirthday(){
				this.isPopup=true;
			}
		}
	};
</script>

<style>
	.address-form .key-name {
		width: 200rpx;
	}

	.address-form .btn-red {
		height: 88rpx;
		line-height: 88rpx;
		border-radius: 44rpx;
		box-shadow: 0 8rpx 16rpx 0 rgba(226, 35, 26, .6);
	}

	.setup-btn {
		position: fixed;
		bottom: 20rpx;
		left: 5%;
		width: 90%;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 80rpx;
		background-color: #fd3826;
		color: #fff;
		font-size: 30rpx;
		display: flex;
		justify-content: center;
		margin: 0 auto;
	}
	.make-item {
		height: 60rpx;
	
	}
	.pop-input {
		width: 100%;
		margin: 26rpx 0;
		box-sizing: border-box;
		border-bottom: 2rpx solid #D9D9D9;
		line-height: 60rpx;
	}
	
	.pop-input input {
		width: 100%;
		padding-left: 15rpx;
	
		font-size: 26rpx;
		color: #333333;
		margin: 16rpx 0;
		text-align: left;
		height: 60rpx;
		line-height: 60rpx;
	}
	
	.pop-input .icon.icon-guanbi {
		width: 38rpx;
		height: 38rpx;
		background-color: #999999;
		color: #FFFFFF;
		font-size: 22rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 50%;
		opacity: 0.8;
	}
</style>
