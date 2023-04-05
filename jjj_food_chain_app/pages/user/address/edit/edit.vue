<template>
	<view class="address-form">
		<form @submit="formSubmit" @reset="formReset">
			<view class="bg-white p-0-30 f30">
				<view class="d-s-c border-b-d9">
					<text class="key-name">收货人</text>
					<input class="ml20 f32 flex-1 p-30-0" name="name" type="text" v-model="address.name" placeholder-class="grary9" placeholder="请输入收货人姓名" />
				</view>
				<view class="d-s-c border-b-d9">
					<text class="key-name">联系方式</text>
					<input class="ml20 f32 flex-1 p-30-0" name="phone" type="text" v-model="address.phone" placeholder-class="grary9" placeholder="请输入收货人手机号" />
				</view>
				<view class="d-s-c border-b-d9">
					<text class="key-name">详细地址</text>
					<view class="input-box flex-1">
						<input
							class="ml20 f32 flex-1 p-30-0"
							name="detail"
							type="text"
							placeholder-class="grary9"
							placeholder="请选择地址"
							v-model="address.detail"
							disabled
							@click="chooseLocation"
						/>
					</view>
				</view>
				<view class="d-s-c border-b-d9">
					<text class="key-name">门牌号</text>
					<textarea
						class="ml20 flex-1 f32 p-30-0 lh150"
						name="address"
						placeholder-class="grary9"
						:auto-height="true"
						v-model="address.address"
						placeholder="请输入街道小区楼牌号等"
					></textarea>
				</view>
			</view>
			<view class="p30"><button type="primary" form-type="submit" class="theme-btn f32 mt60 addBtn">保存</button></view>
		</form>
	</view>
</template>

<script>
export default {
	data() {
		return {
			longitude: 0,
			latitude: 0,
			detail: 0,
			/*地址id*/
			address_id: 0,
			/*地址数据*/
			address: {}
		};
	},
	onLoad(e) {
		this.address_id = e.address_id;
	},
	mounted() {
		/*获取地址数据*/
		this.getData();
	},
	methods: {
		/*获取数据*/
		getData() {
			let self = this;
			let address_id = self.address_id;
			self._get(
				'user.address/detail',
				{
					address_id: address_id
				},
				function(res) {
					self.address = res.data.detail;
					self.address_id = res.data.detail.address_id;
				}
			);
		},
		chooseLocation(n) {
			let self = this;
			uni.chooseLocation({
				success: function(res) {
					self.address.longitude = res.longitude;
					self.address.latitude = res.latitude;
					self.address.detail = res.address;
					console.log(res);
					console.log('位置名称：' + res.name);
					console.log('详细地址：' + res.address);
					console.log('纬度：' + res.latitude);
					console.log('经度：' + res.longitude);
				},
				fail(err) {
					// #ifdef H5
					setTimeout(function() {
						uni.showModal({
							content: '获取定位失败',
							confirmText: '使用默认地址',
							cancelText: '打开文档',
							success(e) {
								if (e.confirm) {
									self.address.longitude = 114.329224;
									self.address.latitude = 30.571721;
									self.address.detail = '湖北省武汉市汉阳区龙阳大道';
								}
								if (e.cancel) {
									window.location.href = 'https://www.baidu.com';
								}
							}
						});
					}, 1000);
					// #endif

					console.log(err);
				}
			});
		},
		/*提交地址*/
		formSubmit: function(e) {
			let self = this;
			var formdata = e.detail.value;
			formdata.longitude = self.address.longitude;
			formdata.latitude = self.address.latitude;
			formdata.address_id = self.address_id;
			if (formdata.name == '') {
				uni.showToast({
					title: '请输入收货人姓名',
					duration: 1000,
					icon: 'none'
				});
				return false;
			}

			if (formdata.phone == '') {
				uni.showToast({
					title: '请输入手机号码',
					duration: 1000,
					icon: 'none'
				});
				return false;
			}

			self._post('user.address/edit', formdata, function(res) {
				self.showSuccess(res.msg, function() {
					// #ifdef  H5
					uni.navigateBack({
						delta: 2
					});
					//#endif
					// #ifndef  H5
					uni.navigateBack({
						delta: 1
					});
					//#endif
				});
			});
		},

		/*清空数据*/
		formReset: function(e) {
			console.log('清空数据');
		},

		/*三级联动选择*/
		showMulLinkageThreePicker() {
			this.$refs.mpvueCityPicker.show();
		},

		/*选择之后绑定*/
		onConfirm(e) {
			this.region = e.label.split(',');
			this.selectCity = e.label;
			this.province_id = e.cityCode[0];
			this.city_id = e.cityCode[1];
			this.region_id = e.cityCode[2];
		}
	}
};
</script>

<style lang="scss">
page {
	background-color: #ffffff;
}

.address-form {
	border-top: 16rpx solid #f2f2f2;
}

.address-form .key-name {
	width: 140rpx;
	font-size: 32rpx;
}

.address-form .btn-red {
	height: 88rpx;
	line-height: 88rpx;
	border-radius: 44rpx;
	box-shadow: 0 8rpx 16rpx 0 rgba(226, 35, 26, 0.6);
}

.addBtn {
	height: 80rpx;
	line-height: 80rpx;
	border-radius: 40rpx;
}
</style>
