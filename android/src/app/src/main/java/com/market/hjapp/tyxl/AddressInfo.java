package com.market.hjapp.tyxl;

import java.io.Serializable;

import android.graphics.Bitmap;

public class AddressInfo implements Serializable {

	public String AddressId;
	public String UserAddress;
	public String Receiver;
	public String Tel;
	public boolean IsChecked;
	public Bitmap ModifyMap;
	public Bitmap DeleteMap;

	public String getAddressId() {
		return AddressId;
	}

	public void setAddressId(String AddressId) {
		this.AddressId = AddressId;
	}

	public String getUserAddress() {
		return UserAddress;
	}

	public void setUserAddress(String UserAddress) {
		this.UserAddress = UserAddress;
	}

	public String getReceiver() {
		return Receiver;
	}

	public void setReceiver(String Receiver) {
		this.Receiver = Receiver;
	}
	public String getTel() {
		return Tel;
	}

	public void setTel(String Tel) {
		this.Tel = Tel;
	}
	public boolean getIsChecked() {
		return IsChecked;
	}

	public void setIsChecked(boolean IsChecked) {
		this.IsChecked = IsChecked;
	}

	public Bitmap getModifyMap() {
		return ModifyMap;
	}

	public void setModifyMap(Bitmap ModifyMap) {
		this.ModifyMap = ModifyMap;
	}

	public Bitmap getDeleteMap() {
		return DeleteMap;
	}

	public void setDeleteMap(Bitmap DeleteMap) {
		this.DeleteMap = DeleteMap;
	}
}
