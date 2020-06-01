// @flow
import React from "react";
import StarRating from "react-native-star-rating";

import { Images, Metrics } from "../../theme";

export default class Rating extends React.Component {
  render() {
    const { ...rest } = this.props;
    return (
      <StarRating
        maxStars={5}
        starStyle={{ marginRight: Metrics.smallMargin }}
        emptyStar={Images.emptyStar}
        fullStar={Images.fullStar}
        halfStar={Images.fullStar}
        {...rest}
      />
    );
  }
}
