<template>
	<view class="address-form">
		<form @submit="formSubmit" @reset="formReset">
			<view class="bg-white p-0-30 f30">
				<view class="d-s-c border-b-d9">
					<text class="key-name">收货人</text>
					<input class="ml20 flex-1 f32 p-30-0" name="name" type="text" placeholder-class="grary9" v-model="address.name" placeholder="请输入收货人姓名" />
				</view>
				<view class="d-s-c border-b-d9">
					<text class="key-name">联系方式</text>
					<input class="ml20 flex-1 f32 p-30-0" name="phone" type="text" placeholder-class="grary9" v-model="address.phone" placeholder="请输入收货人手机号" />
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
						class="ml20 flex-1 p-30-0 lh150"
						name="address"
						:auto-height="true"
						v-model="address.address"
						placeholder-class="grary9"
						placeholder="请输入街道小区楼牌号等"
					></textarea>
				</view>
			</view>
			<view class="p30"><button type="primary" form-type="submit" class="theme-btn f32 mt60 addBtn">保存</button></view>
		</form>
		<mpvue-city-picker ref="mpvueCityPicker" :pickerValueDefault="cityPickerValueDefault" @onConfirm="onConfirm"></mpvue-city-picker>
	</view>
</template>

<script>
import mpvueCityPicker from '@/components/mpvue-citypicker/mpvueCityPicker.vue';
export default {
	components: {
		mpvueCityPicker
	},
	data() {
		return {
			cityPickerValueDefault: [0, 0, 0],
			selectCity: '选择省,市,区',
			province_id: 0,
			city_id: 0,
			region_id: 0,
			address: {
				detail:''
			},
			delta: 1
		};
	},
	onLoad: function(options) {
		this.delta = options.delta;
		console.log(this.delta)
	},
	methods: {
		chooseLocation(n){
			let self=this;
			uni.chooseLocation({
			    success: function (res) {
					self.address.longitude=res.longitude;
					self.address.latitude=res.latitude;
					self.address.detail=res.address;
					console.log(res)
			        console.log('位置名称：' + res.name);
			        console.log('详细地址：' + res.address);
			        console.log('纬度：' + res.latitude);
			        console.log('经度：' + res.longitude);
			    },
				fail(err) {
					// #ifdef H5
					setTimeout(function() {
						uni.showModal({
							content:'获取定位失败',
							confirmText:'使用默认地址',
							cancelText:'打开文档',
							success(e) {
								if(e.confirm){
									self.address.longitude = 114.329224;
									self.address.latitude = 30.571721;
									self.address.detail = '湖北省武汉市汉阳区龙阳大道';
								}
								if(e.cancel){
									window.location.href='https://www.baidu.com'
								}
							}
						})
					}, 1000);
					// #endif

					console.log(err)
				}
			});
		},
		/*提交*/
		formSubmit: function(e) {
			let self = this;
			var formdata = e.detail.value;
			formdata.latitude = self.address.latitude;
			formdata.longitude = self.address.longitude;
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

			/*let reg = /^((0\d{2,3}-\d{7,8})|(1[3456789]\d{9}))$/;
			if (!reg.test(formdata.phone)) {
				uni.showToast({
					title: '手机号码格式不正确',
					duration: 1000,
					icon: 'none'
				});
				return false;
			}*/

			if (formdata.latitude == 0  || formdata.longitude == 0) {
				if (formdata.detail == '') {
					uni.showToast({
						title: '请选择正确的地址',
						duration: 1000,
						icon: 'none'
					});
					return false;
				}
			}


			self._post('user.address/add', formdata, function(res) {
				self.showSuccess(res.msg, function() {
					// #ifndef H5
					uni.navigateBack({
						delta: parseInt(self.delta)
					});
					// #endif
					// #ifdef H5
					history.go(-self.delta);
					// #endif
				});
			});
		},

		formReset: function(e) {
			console.log('清空数据');
		},

		/*三级联动选择*/
		showMulLinkageThreePicker() {
			this.$refs.mpvueCityPicker.show();
		},

		/*确定选择的省市区*/
		onConfirm(e) {
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
	/* border-top: 16rpx solid #f2f2f2; */
}

.address-form .key-name {
	width: 140rpx;
	font-size: 32rpx;
}

.address-form .btn-red {
	height: 88rpx;
	line-height: 88rpx;
	border-radius: 44rpx;
}

.addBtn {
	height: 80rpx;
	line-height: 80rpx;
	border-radius: 40rpx;
}
</style>
