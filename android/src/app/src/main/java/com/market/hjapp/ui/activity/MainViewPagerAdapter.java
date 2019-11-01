
package com.market.hjapp.ui.activity;

import java.util.List;

import android.os.Parcelable;
import android.support.v4.view.PagerAdapter;
import android.support.v4.view.ViewPager;
import android.view.View;

import com.viewpagerindicator.TitleProvider;

public class MainViewPagerAdapter extends PagerAdapter implements TitleProvider
{
	private List<View> mListViews;
	/** 标题内容 */
	private String[] contents;
	private int mCount; // 循环滚动新增
	private boolean isLoop = false;//是否可以循环滑动 以三页为限
	private final int LIMIT_PAGE_COUNT = 3;//限制页数

	public MainViewPagerAdapter(List<View> mListViews, String[] contents)
	{
		this.mListViews = mListViews;
		this.contents = contents;
		mCount = mListViews.size();
//		if (mListViews.size() < LIMIT_PAGE_COUNT)
//		{
//			isLoop = false;
//		}
//		//zxg,20120413,左右循环
//		if(isLoop)
//			mCount*=101;
	}

	@Override
	public void destroyItem(View collection, int position, Object arg2)
	{
//		if (!isLoop)
//		{
//			((ViewPager) collection).removeView(mListViews.get(position));
//			return;
//		}
//		// //循环滚动新增
//		if (position >= mListViews.size())
//		{
//			int newPosition = position % mListViews.size();
//			position = newPosition;
//			// ((ViewPager) collection).removeView(views.get(position));
//		}
//		if (position < 0)
//		{
//			position = -position;
//			// ((ViewPager) collection).removeView(views.get(position));
//		}
	}

	@Override
	public void finishUpdate(View arg0)
	{
	}

	@Override
	public int getCount()
	{
		return mListViews.size();
//		if (!isLoop)
//		{
//			return mListViews.size();
//		}
//		return mCount + 1;// 此处+1才能向右连续滚动
	}

	@Override
	public Object instantiateItem(View collection, int position)
	{
		if (!isLoop)
		{
			if (position<mListViews.size()) {
				((ViewPager) collection).addView(mListViews.get(position), 0);
				return mListViews.get(position);
			}
		}
//		if (position >= mListViews.size())
//		{
//			int newPosition = position % mListViews.size();
//
//			position = newPosition;
//			mCount++;
//		}
//		if (position < 0)
//		{
//			position = -position;
//			mCount--;
//		}
		try
		{
			((ViewPager) collection).addView(mListViews.get(position), 0);
		} catch (Exception e)
		{
		}
		return mListViews.get(position);
	}

	@Override
	public boolean isViewFromObject(View arg0, Object arg1)
	{
		return arg0 == (arg1);
	}

	@Override
	public void restoreState(Parcelable arg0, ClassLoader arg1)
	{
	}

	@Override
	public Parcelable saveState()
	{
		return null;
	}

	@Override
	public void startUpdate(View arg0)
	{
	}

	@Override
	public String getTitle(int position)
	{
		return this.contents[position % this.contents.length];
	}
}