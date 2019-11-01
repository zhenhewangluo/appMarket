
package com.market.hjapp.service.download.entity;

import android.os.Parcel;
import android.os.Parcelable;

public class DownloadProgressInfo implements DownloadInfo {
	private final int type = DownloadInfoType.DOWNLOAD_UPDATE_PROGRESS;
	private int progress;

	public DownloadProgressInfo(){}
	
	public DownloadProgressInfo(int progress) {
		this.progress = progress;
	}
	
	public void setProgress(int progress) {
		this.progress = progress;
	}
	
	public int getProgress() {
		return progress;
	}

	@Override
	public String getInfoDesc() {
		return "UPDATE_PROGRESS";
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
		dest.writeInt(progress);
	}
	
	private DownloadProgressInfo(Parcel parcel){
		progress = parcel.readInt();
	}

	public static final Creator<DownloadProgressInfo> CREATOR
			= new Creator<DownloadProgressInfo>() {
		@Override
		public DownloadProgressInfo createFromParcel(Parcel arg0) {
			return new DownloadProgressInfo(arg0);
		}

		@Override
		public DownloadProgressInfo[] newArray(int arg0) {
			return new DownloadProgressInfo[arg0];
		}
	};

}
