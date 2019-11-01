
package com.market.hjapp.service.download;

import com.market.hjapp.service.download.entity.DownloadErrorInfo;

public interface OnDownloadStatusChangedListener{
    void onProgressUpdate(int value, boolean needBroadcast);
    
    void onPreDownload();
    
//    void onPostDownloaded(int appDbId, AppContentProvider provider);
    void onPostDownloaded(String appName, String localPath, int pid);
    
    void onDownloadError(DownloadErrorInfo downErrorInfo);
    
    void onStopDownload();
}