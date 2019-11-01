
package com.market.hjapp.service.download.entity;

import android.os.Parcel;
import android.os.Parcelable;

public class DownloadErrorInfo implements DownloadInfo {
	private final int type = DownloadInfoType.DOWNLOAD_ERROR;
	private int errorType;
	private String appName;
	private int appDbId;

	// 错误类型
	public static final int NOT_APK_PACKAGE = 1;
	public static final int IO_ERROR = 2;
	public static final int TIME_OUT = 3;
	public static final int FILE_NOT_FOUND = 4;
	public static final int UNKNOWN_HOST = 5;
	public static final int CLIENT_PROTOCOL = 6;
	public static final int JSON_ERROR = 7;

	public DownloadErrorInfo() {
	}

	public DownloadErrorInfo(int errorType) {
		this.errorType = errorType;
	}

	public void setErrorType(int errorType) {
		this.errorType = errorType;
	}
	
	public int getErrorType() {
		return errorType;
	}
	
	public String getAppName() {
		return appName;
	}

	public void setAppName(String appName) {
		this.appName = appName;
	}
	
	public void setAppDbId(int appDbId) {
		this.appDbId = appDbId;
	}
	
	public int getAppDbId() {
		return appDbId;
	}

	@Override
	public String getInfoDesc() {
		return "DOWNLOAD_ERROR";
	}

	@Override
	public int getInfoType() {
		return type;
	}

	@Override
	public int describeContents() {
		return 0;
	}

	@Override
	public void writeToParcel(Parcel dest, int flags) {
		dest.writeInt(errorType);
		dest.writeString(appName);
		dest.writeInt(appDbId);
	}

	public static final Creator<DownloadErrorInfo> CREATOR = new Creator<DownloadErrorInfo>() {

		@Override
		public DownloadErrorInfo createFromParcel(Parcel source) {
			return new DownloadErrorInfo(source);
		}

		@Override
		public DownloadErrorInfo[] newArray(int size) {
			return new DownloadErrorInfo[size];
		}

	};

	private DownloadErrorInfo(Parcel source){
		errorType = source.readInt();
		appName = source.readString();
		appDbId = source.readInt();
	}
}
