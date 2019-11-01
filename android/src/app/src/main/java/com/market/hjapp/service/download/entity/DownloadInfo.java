
package com.market.hjapp.service.download.entity;

import android.os.Parcelable;

public interface DownloadInfo extends Parcelable{
	int getInfoType();
	String getInfoDesc();
}
